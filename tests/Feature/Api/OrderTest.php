<?php

namespace Tests\Feature\Api;

use App\Models\Customer;
use App\Models\Order;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class OrderTest extends TestCase
{
    private function validOrderPayload(int $variantId): array
    {
        return [
            'line_items'       => [['variant_id' => $variantId, 'quantity' => 1]],
            'shipping_address' => [
                'first_name' => 'Test',
                'last_name'  => 'User',
                'address1'   => '123 Main St',
                'city'       => 'Karachi',
                'country'    => 'PK',
                'zip'        => '75000',
            ],
            'email' => 'guest@example.com',
        ];
    }

    // ── Store ─────────────────────────────────────────────────────────────────

    public function test_guest_can_place_order(): void
    {
        Event::fake();
        [, $variant] = $this->makeProductWithVariant();

        $res = $this->postJson('/api/v1/orders', $this->validOrderPayload($variant->id));

        $res->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['order' => ['id', 'order_number', 'financial_status']]]);

        $this->assertDatabaseHas('orders', ['email' => 'guest@example.com']);
    }

    public function test_authenticated_customer_can_place_order(): void
    {
        Event::fake();
        [, $variant] = $this->makeProductWithVariant();
        [$customer, $token] = $this->customerWithToken();

        $payload = array_merge($this->validOrderPayload($variant->id), [
            'customer_id' => $customer->id,
            'email'       => $customer->email,
        ]);

        $this->postJson('/api/v1/orders', $payload, $this->authHeaders($token))
            ->assertStatus(201)
            ->assertJsonPath('data.order.financial_status', 'pending');
    }

    public function test_order_requires_line_items(): void
    {
        $this->postJson('/api/v1/orders', [
            'email'            => 'test@example.com',
            'shipping_address' => ['first_name' => 'Test'],
        ])->assertStatus(422)->assertJsonValidationErrors(['line_items']);
    }

    public function test_order_requires_shipping_address(): void
    {
        [, $variant] = $this->makeProductWithVariant();

        $this->postJson('/api/v1/orders', [
            'line_items' => [['variant_id' => $variant->id, 'quantity' => 1]],
        ])->assertStatus(422)->assertJsonValidationErrors(['shipping_address']);
    }

    public function test_order_fails_when_out_of_stock(): void
    {
        Event::fake();
        [, $variant] = $this->makeProductWithVariant([], ['inventory_quantity' => 0]);

        $this->postJson('/api/v1/orders', $this->validOrderPayload($variant->id))
            ->assertStatus(422)
            ->assertJsonPath('success', false);
    }

    public function test_order_fails_for_invalid_variant(): void
    {
        $this->postJson('/api/v1/orders', [
            'line_items'       => [['variant_id' => 99999, 'quantity' => 1]],
            'shipping_address' => ['first_name' => 'Test'],
            'email'            => 'x@x.com',
        ])->assertStatus(422);
    }

    // ── Show ──────────────────────────────────────────────────────────────────

    public function test_customer_can_view_own_order(): void
    {
        Event::fake();
        [, $variant] = $this->makeProductWithVariant();
        [$customer, $token] = $this->customerWithToken();

        $placeRes = $this->postJson('/api/v1/orders', array_merge(
            $this->validOrderPayload($variant->id),
            ['customer_id' => $customer->id, 'email' => $customer->email]
        ), $this->authHeaders($token));

        $orderId = $placeRes->json('data.order.id');

        $this->getJson("/api/v1/orders/{$orderId}", $this->authHeaders($token))
            ->assertStatus(200)
            ->assertJsonPath('data.order.id', $orderId);
    }

    public function test_customer_cannot_view_other_customers_order(): void
    {
        Event::fake();
        [, $variant] = $this->makeProductWithVariant();

        // Order placed by customer A
        [$customerA] = $this->customerWithToken();
        $placeRes = $this->postJson('/api/v1/orders', array_merge(
            $this->validOrderPayload($variant->id),
            ['customer_id' => $customerA->id, 'email' => $customerA->email]
        ));
        $orderId = $placeRes->json('data.order.id');

        // Customer B tries to view it
        [$customerB, $tokenB] = $this->customerWithToken();
        $this->getJson("/api/v1/orders/{$orderId}", $this->authHeaders($tokenB))
            ->assertStatus(404);
    }

    // ── Cancel ────────────────────────────────────────────────────────────────

    public function test_customer_can_cancel_pending_order(): void
    {
        Event::fake();
        [, $variant] = $this->makeProductWithVariant();
        [$customer, $token] = $this->customerWithToken();
        $headers = $this->authHeaders($token);

        $placeRes = $this->postJson('/api/v1/orders', array_merge(
            $this->validOrderPayload($variant->id),
            ['customer_id' => $customer->id, 'email' => $customer->email]
        ), $headers);

        $orderId = $placeRes->json('data.order.id');

        $this->postJson("/api/v1/orders/{$orderId}/cancel", [], $headers)
            ->assertStatus(200)
            ->assertJsonPath('data.order.financial_status', 'voided');
    }

    public function test_customer_cannot_cancel_fulfilled_order(): void
    {
        Event::fake();
        [, $variant] = $this->makeProductWithVariant();
        [$customer, $token] = $this->customerWithToken();
        $headers = $this->authHeaders($token);

        $placeRes = $this->postJson('/api/v1/orders', array_merge(
            $this->validOrderPayload($variant->id),
            ['customer_id' => $customer->id, 'email' => $customer->email]
        ), $headers);

        $orderId = $placeRes->json('data.order.id');
        Order::where('id', $orderId)->update(['fulfillment_status' => 'fulfilled']);

        $this->postJson("/api/v1/orders/{$orderId}/cancel", [], $headers)
            ->assertStatus(422);
    }
}
