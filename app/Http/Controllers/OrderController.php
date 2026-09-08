<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use SePay\Builders\CheckoutBuilder;
use SePay\SePayClient;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('items.product')
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(8);

        return view('orders.index', compact('orders'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'payment_method' => ['required', 'in:COD,online'],
        ]);
        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống.');
        }

        try {
            $order = DB::transaction(function () use ($cart, $data) {
                $total = 0;
                $lines = [];

                foreach ($cart as $id => $item) {
                    $product = Product::lockForUpdate()->find($id);

                    if (! $product || $item['quantity'] > $product->quantity) {
                        throw new \RuntimeException("Sản phẩm {$item['name']} không đủ tồn kho.");
                    }

                    $quantity = (int) $item['quantity'];
                    $price = (float) $product->price;
                    $total += $price * $quantity;
                    $lines[] = compact('product', 'quantity', 'price');
                }

                $order = Order::create([
                    'user_id' => Auth::id(),
                    'total' => $total,
                    'status' => 'processing',
                    'payment_method' => $data['payment_method'],
                ]);

                foreach ($lines as $line) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $line['product']->id,
                        'quantity' => $line['quantity'],
                        'price' => $line['price'],
                    ]);
                    $line['product']->decrement('quantity', $line['quantity']);
                }

                return $order;
            });

            session()->forget('cart');

            if ($data['payment_method'] === 'online') {
                session()->put('bank_transfer_total', $order->total);
                session()->put('bank_transfer_order_id', $order->id);

                return redirect()->route('checkout.bank-transfer');
            }

            return redirect()->route('orders.index')->with('success', "Đặt hàng COD #{$order->id} thành công.");
        } catch (\Throwable $exception) {
            report($exception);

            return redirect()->route('cart.index')->with('error', 'Không thể xử lý đơn hàng. Vui lòng kiểm tra lại giỏ hàng và thử lại.');
        }
    }

    public function bankTransfer()
    {
        $totalAmount = session('bank_transfer_total');
        $orderId = session('bank_transfer_order_id');

        if ($totalAmount === null || $orderId === null) {
            return redirect()->route('orders.index')->with('error', 'Không tìm thấy thông tin thanh toán.');
        }

        $sepay = new SePayClient(
            (string) config('services.sepay.merchant_id'),
            (string) config('services.sepay.secret_key'),
            SePayClient::ENVIRONMENT_SANDBOX,
        );

        // WAF cua sepay.vn chan request chua 127.0.0.1 / localhost hoac scheme http.
        // route() sinh URL tu host cua request hien tai, nen khi dev qua tunnel no van
        // co the ra http://127.0.0.1:8000. Vi vay lay goc URL tu config('app.url').
        $callbackUrl = static function (string $result): string {
            return rtrim((string) config('app.url'), '/')
                . route('sepay.callback', ['result' => $result], absolute: false);
        };

        $successUrl = $callbackUrl('success');
        $errorUrl = $callbackUrl('error');
        $cancelUrl = $callbackUrl('cancel');

        foreach (['success_url' => $successUrl, 'error_url' => $errorUrl, 'cancel_url' => $cancelUrl] as $name => $url) {
            if (! str_starts_with($url, 'https://') || preg_match('/^https:\/\/(127\.0\.0\.1|localhost|0\.0\.0\.0)\b/i', $url)) {
                Log::warning('SePay checkout: ' . $name . ' co the bi WAF chan (can https va host public).', [
                    'url' => $url,
                    'app_url' => config('app.url'),
                ]);
            }
        }

        // Cac URL nay nam trong chuoi ky, nen phai gan vao builder TRUOC khi
        // generateFormFields() tinh signature (signature duoc tinh o buoc cuoi ben duoi).
        $checkoutData = CheckoutBuilder::make()
            ->paymentMethod('BANK_TRANSFER')
            ->currency('VND')
            ->orderInvoiceNumber('CHODCU-' . $orderId)
            ->orderAmount((int) round((float) $totalAmount))
            ->operation('PURCHASE')
            ->orderDescription('Thanh toan don hang #' . $orderId)
            ->customerId((string) Auth::id())
            ->successUrl($successUrl)
            ->errorUrl($errorUrl)
            ->cancelUrl($cancelUrl)
            ->build();

        // generateFormFields() ky tren toan bo form fields (bao gom 3 URL o tren),
        // nen signature luon duoc tinh SAU khi URL da duoc thay.
        $fields = $sepay->checkout()->generateFormFields($checkoutData);

        return view('checkout.bank-transfer', [
            'fields' => $fields,
            'checkoutUrl' => config('services.sepay.checkout_url'),
            'orderId' => $orderId,
            'totalAmount' => $totalAmount,
        ]);
    }
}
