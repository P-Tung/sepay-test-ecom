<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SePayWebhookTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('services.sepay.secret_key', 'test-secret-key');
    }

    public function test_sepay_ipn_marks_a_matching_order_as_paid(): void
    {
        $order = Order::create([
            'user_id' => User::factory()->create()->id,
            'total' => 100000,
            'status' => 'processing',
            'payment_method' => 'online',
        ]);

        $response = $this->withHeaders(['X-Secret-Key' => 'test-secret-key'])
            ->postJson(route('sepay.ipn'), [
                'notification_type' => 'ORDER_PAID',
                'order' => [
                    'order_invoice_number' => 'CHODCU-' . $order->id,
                    'order_amount' => '100000.00',
                ],
                'transaction' => [
                    'transaction_id' => 'sandbox-tx-1',
                    'transaction_status' => 'APPROVED',
                ],
            ]);

        $response->assertOk()->assertJson(['success' => true]);
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'paid',
            'transaction_id' => 'sandbox-tx-1',
        ]);
    }

    public function test_sepay_ipn_does_not_mark_an_underpaid_order_as_paid(): void
    {
        $order = Order::create([
            'user_id' => User::factory()->create()->id,
            'total' => 100000,
            'status' => 'processing',
            'payment_method' => 'online',
        ]);

        $this->withHeaders(['X-Secret-Key' => 'test-secret-key'])
            ->postJson(route('sepay.ipn'), [
                'notification_type' => 'ORDER_PAID',
                'order' => [
                    'order_invoice_number' => 'CHODCU-' . $order->id,
                    'order_amount' => '99999',
                ],
                'transaction' => [
                    'transaction_id' => 'sandbox-tx-2',
                    'transaction_status' => 'APPROVED',
                ],
            ])->assertOk();

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'processing',
            'transaction_id' => null,
        ]);
    }

    public function test_secret_key_header_takes_priority_over_an_optional_signature(): void
    {
        $order = Order::create([
            'user_id' => User::factory()->create()->id,
            'total' => 100000,
            'status' => 'processing',
            'payment_method' => 'online',
        ]);

        $response = $this->withHeaders([
            'X-Secret-Key' => 'test-secret-key',
        ])->postJson(route('sepay.ipn'), [
            'signature' => 'gateway-signature-field',
            'notification_type' => 'ORDER_PAID',
            'order' => [
                'order_invoice_number' => 'CHODCU-' . $order->id,
                'order_amount' => '100000.00',
            ],
            'transaction' => [
                'transaction_id' => 'sandbox-tx-3',
                'transaction_status' => 'APPROVED',
            ],
        ]);

        $response->assertOk()->assertJson(['success' => true]);
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'paid',
            'transaction_id' => 'sandbox-tx-3',
        ]);
    }

    public function test_secret_key_is_accepted_as_an_apikey_authorization_header(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Apikey test-secret-key',
        ])->postJson(route('sepay.ipn'), [
            'notification_type' => 'TRANSACTION_VOID',
            'order' => [
                'order_invoice_number' => 'CHODCU-999',
                'order_amount' => '100000.00',
            ],
            'transaction' => [
                'transaction_status' => 'VOIDED',
            ],
        ]);

        $response->assertOk()->assertJson(['success' => true]);
    }
}
