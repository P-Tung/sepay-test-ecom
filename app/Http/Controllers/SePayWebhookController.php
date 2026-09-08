<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SePayWebhookController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $expectedKey = (string) config('services.sepay.webhook_key');
        $authorization = (string) $request->header('Authorization');
        $providedKey = preg_replace('/^Apikey\s+/i', '', $authorization);

        if ($expectedKey === '' || ! hash_equals($expectedKey, (string) $providedKey)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $payload = $request->validate([
            'id' => ['required'],
            'content' => ['required', 'string'],
            'transferType' => ['required', 'in:in,out'],
            'transferAmount' => ['required', 'numeric', 'min:0'],
        ]);

        if ($payload['transferType'] !== 'in') {
            return response()->json(['success' => true]);
        }

        if (Order::where('transaction_id', (string) $payload['id'])->exists()) {
            return response()->json(['success' => true]);
        }

        if (! preg_match('/CHODCU[-\s]?(\d+)/i', $payload['content'], $matches)) {
            return response()->json(['success' => true]);
        }

        $updated = DB::transaction(function () use ($payload, $matches) {
            $order = Order::lockForUpdate()->find((int) $matches[1]);

            if (! $order || $order->status !== 'processing' || $order->payment_method !== 'online'
                || (int) $payload['transferAmount'] < (int) round((float) $order->total)) {
                return false;
            }

            $order->update([
                'status' => 'paid',
                'transaction_id' => (string) $payload['id'],
            ]);

            return true;
        });

        return response()->json(['success' => true, 'matched' => $updated]);
    }

    /**
     * IPN cua SePay Payment Gateway.
     *
     * Luon tra HTTP 200 khi da xu ly xong (ke ca khi bo qua) de SePay khong retry lien tuc.
     * Chi tra 403 khi chu ky khong hop le - truong hop do khong dung toi don hang.
     */
    public function ipn(Request $request): JsonResponse
    {
        if (! $this->ipnSignatureIsValid($request)) {
            Log::warning('SePay IPN: chu ky khong hop le, tu choi request.', [
                'ip' => $request->ip(),
                'invoice' => (string) $request->input('order.order_invoice_number'),
            ]);

            return response()->json(['success' => false, 'message' => 'Invalid signature'], 403);
        }

        $request->validate([
            'order.order_invoice_number' => ['required', 'string'],
            'order.order_amount' => ['required', 'numeric'],
            'transaction.transaction_status' => ['required', 'string'],
            'transaction.transaction_id' => ['nullable', 'string'],
            'transaction.id' => ['nullable', 'string'],
        ]);

        // SePay dat ten field su kien la "event" hoac "notification_type" tuy phien ban payload.
        $event = (string) ($request->input('event') ?? $request->input('notification_type') ?? '');
        $status = (string) $request->input('transaction.transaction_status');

        if ($event !== 'ORDER_PAID' || $status !== 'APPROVED') {
            return response()->json(['success' => true, 'result' => 'ignored_event']);
        }

        $invoice = (string) $request->input('order.order_invoice_number');

        if (! preg_match('/^CHODCU-(\d+)$/', $invoice, $matches)) {
            Log::warning('SePay IPN: order_invoice_number sai dinh dang.', ['invoice' => $invoice]);

            return response()->json(['success' => true, 'result' => 'invalid_invoice_format']);
        }

        $orderId = (int) $matches[1];
        $paidAmount = (int) round((float) $request->input('order.order_amount'));
        $transactionId = $request->input('transaction.transaction_id') ?? $request->input('transaction.id');

        $result = DB::transaction(function () use ($orderId, $paidAmount, $transactionId, $invoice): string {
            $order = Order::whereKey($orderId)->lockForUpdate()->first();

            if (! $order) {
                Log::warning('SePay IPN: khong tim thay don hang.', ['invoice' => $invoice, 'order_id' => $orderId]);

                return 'order_not_found';
            }

            // Idempotent: don da paid roi thi khong lam gi them.
            if ($order->status === 'paid') {
                return 'already_paid';
            }

            if ($order->payment_method !== 'online' || $order->status !== 'processing') {
                Log::warning('SePay IPN: don hang khong o trang thai cho thanh toan online.', [
                    'order_id' => $orderId,
                    'status' => $order->status,
                    'payment_method' => $order->payment_method,
                ]);

                return 'not_payable';
            }

            $expectedAmount = (int) round((float) $order->total);

            if ($paidAmount !== $expectedAmount) {
                Log::warning('SePay IPN: so tien lech, KHONG danh dau paid.', [
                    'order_id' => $orderId,
                    'expected_total' => $expectedAmount,
                    'ipn_order_amount' => $paidAmount,
                ]);

                return 'amount_mismatch';
            }

            $order->update([
                'status' => 'paid',
                'transaction_id' => $transactionId !== null ? (string) $transactionId : null,
            ]);

            return 'paid';
        });

        if ($result === 'paid') {
            Log::info('SePay IPN: da danh dau don hang paid.', [
                'order_id' => $orderId,
                'amount' => $paidAmount,
                'transaction_id' => $transactionId,
            ]);
        }

        return response()->json(['success' => true, 'result' => $result]);
    }

    /**
     * Xac thuc IPN truoc khi dung toi don hang.
     *
     * Chap nhan mot trong hai bang chung:
     *  1. Field "signature" (hoac header X-Sepay-Signature) - HMAC-SHA256 base64 bang SEPAY_SECRET_KEY.
     *     Neu payload CO signature thi bat buoc phai khop, khong the vong qua bang Apikey.
     *  2. Header "X-Secret-Key: <SEPAY_SECRET_KEY>" - co che SePay dung cho IPN.
     *  3. Header "Authorization: Apikey <SEPAY_WEBHOOK_KEY>" - co che webhook tuy chon.
     * Khong co bang chung nao hop le -> false -> 403.
     */
    private function ipnSignatureIsValid(Request $request): bool
    {
        $secretKey = (string) config('services.sepay.secret_key');
        $providedSecretKey = (string) $request->header('X-Secret-Key', '');

        if ($secretKey !== '' && $providedSecretKey !== '' && hash_equals($secretKey, $providedSecretKey)) {
            return true;
        }

        $signature = (string) ($request->input('signature') ?? $request->header('X-Sepay-Signature', '') ?? '');

        if ($signature !== '') {
            $secret = (string) config('services.sepay.secret_key');

            if ($secret === '') {
                Log::warning('SePay IPN: SEPAY_SECRET_KEY chua duoc cau hinh, khong the verify signature.');

                return false;
            }

            $canonical = $this->canonicalIpnPayload($request->except(['signature']));
            $expected = base64_encode(hash_hmac('sha256', $canonical, $secret, true));

            if (hash_equals($expected, $signature)) {
                return true;
            }

            // Log chuoi ky de doi chieu voi tai lieu SePay neu canonical form khac.
            Log::warning('SePay IPN: HMAC khong khop.', [
                'canonical' => $canonical,
                'expected' => $expected,
                'received' => $signature,
            ]);

            return false;
        }

        $webhookKey = (string) config('services.sepay.webhook_key');
        $providedKey = (string) preg_replace('/^Apikey\s+/i', '', (string) $request->header('Authorization', ''));

        return $webhookKey !== '' && $providedKey !== '' && hash_equals($webhookKey, $providedKey);
    }

    /**
     * Chuoi canonical de tinh HMAC: flatten payload theo dot-notation, sort key,
     * noi "key=value" bang dau phay (cung dang voi signCheckoutFields cua SDK).
     *
     * @param  array<string, mixed>  $payload
     */
    private function canonicalIpnPayload(array $payload): string
    {
        $flat = Arr::dot($payload);
        ksort($flat);

        $parts = [];

        foreach ($flat as $key => $value) {
            if (is_bool($value)) {
                $value = $value ? 'true' : 'false';
            } elseif (! is_scalar($value) && $value !== null) {
                $value = json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            }

            $parts[] = $key . '=' . (string) $value;
        }

        return implode(',', $parts);
    }
}
