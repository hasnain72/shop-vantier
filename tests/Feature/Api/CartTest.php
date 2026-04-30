<?php

namespace Tests\Feature\Api;

use App\Models\DiscountCode;
use App\Models\PriceRule;
use Tests\TestCase;

class CartTest extends TestCase
{
    // ── Add to Cart ───────────────────────────────────────────────────────────

    public function test_guest_can_add_item_to_cart(): void
    {
        [, $variant] = $this->makeProductWithVariant();

        $res = $this->postJson('/api/v1/cart/add', [
            'items' => [['variant_id' => $variant->id, 'quantity' => 2]],
        ]);

        $res->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['cart_token', 'data' => ['items']]);
    }

    public function test_authenticated_customer_can_add_item(): void
    {
        [, $variant] = $this->makeProductWithVariant();
        [$customer, $token] = $this->customerWithToken();

        $this->postJson('/api/v1/cart/add', [
            'items' => [['variant_id' => $variant->id, 'quantity' => 1]],
        ], $this->authHeaders($token))
            ->assertStatus(201)
            ->assertJsonPath('data.items.0.variant_id', $variant->id);
    }

    public function test_adding_same_variant_increments_quantity(): void
    {
        [, $variant] = $this->makeProductWithVariant();
        [$customer, $token] = $this->customerWithToken();

        $headers = $this->authHeaders($token);

        $this->postJson('/api/v1/cart/add', [
            'items' => [['variant_id' => $variant->id, 'quantity' => 2]],
        ], $headers);

        $this->postJson('/api/v1/cart/add', [
            'items' => [['variant_id' => $variant->id, 'quantity' => 3]],
        ], $headers);

        $res = $this->getJson('/api/v1/cart', $headers);
        $res->assertStatus(200)
            ->assertJsonPath('data.items.0.quantity', 5);
    }

    public function test_cannot_add_out_of_stock_item(): void
    {
        [, $variant] = $this->makeProductWithVariant([], ['inventory_quantity' => 0, 'inventory_policy' => 'deny']);

        $this->postJson('/api/v1/cart/add', [
            'items' => [['variant_id' => $variant->id, 'quantity' => 1]],
        ])->assertStatus(422);
    }

    public function test_add_validates_required_fields(): void
    {
        $this->postJson('/api/v1/cart/add', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['items']);
    }

    // ── Show Cart ─────────────────────────────────────────────────────────────

    public function test_guest_cart_not_found_without_token(): void
    {
        $this->getJson('/api/v1/cart')
            ->assertStatus(404);
    }

    public function test_can_view_cart_with_cart_token_header(): void
    {
        [, $variant] = $this->makeProductWithVariant();

        $addRes = $this->postJson('/api/v1/cart/add', [
            'items' => [['variant_id' => $variant->id, 'quantity' => 1]],
        ]);

        $cartToken = $addRes->json('cart_token');

        $this->getJson('/api/v1/cart', ['X-Cart-Token' => $cartToken, 'Accept' => 'application/json'])
            ->assertStatus(200)
            ->assertJsonCount(1, 'data.items');
    }

    // ── Update Cart ───────────────────────────────────────────────────────────

    public function test_can_update_cart_item_quantity(): void
    {
        [, $variant] = $this->makeProductWithVariant();
        [$customer, $token] = $this->customerWithToken();
        $headers = $this->authHeaders($token);

        $addRes = $this->postJson('/api/v1/cart/add', [
            'items' => [['variant_id' => $variant->id, 'quantity' => 2]],
        ], $headers);

        $itemId = $addRes->json('data.items.0.id');

        $this->postJson('/api/v1/cart/update', [
            'updates' => [$itemId => 5],
        ], $headers)
            ->assertStatus(200)
            ->assertJsonPath('data.items.0.quantity', 5);
    }

    public function test_setting_quantity_to_zero_removes_item(): void
    {
        [, $variant] = $this->makeProductWithVariant();
        [$customer, $token] = $this->customerWithToken();
        $headers = $this->authHeaders($token);

        $addRes = $this->postJson('/api/v1/cart/add', [
            'items' => [['variant_id' => $variant->id, 'quantity' => 1]],
        ], $headers);

        $itemId = $addRes->json('data.items.0.id');

        $this->postJson('/api/v1/cart/update', ['updates' => [$itemId => 0]], $headers)
            ->assertStatus(200)
            ->assertJsonCount(0, 'data.items');
    }

    // ── Remove Item ───────────────────────────────────────────────────────────

    public function test_can_remove_item_from_cart(): void
    {
        [, $variant] = $this->makeProductWithVariant();
        [$customer, $token] = $this->customerWithToken();
        $headers = $this->authHeaders($token);

        $addRes = $this->postJson('/api/v1/cart/add', [
            'items' => [['variant_id' => $variant->id, 'quantity' => 1]],
        ], $headers);

        $itemId = $addRes->json('data.items.0.id');

        $this->postJson('/api/v1/cart/remove', ['line_item_id' => $itemId], $headers)
            ->assertStatus(200)
            ->assertJsonCount(0, 'data.items');
    }

    // ── Clear Cart ────────────────────────────────────────────────────────────

    public function test_can_clear_cart(): void
    {
        [, $v1] = $this->makeProductWithVariant();
        [, $v2] = $this->makeProductWithVariant();
        [$customer, $token] = $this->customerWithToken();
        $headers = $this->authHeaders($token);

        $this->postJson('/api/v1/cart/add', [
            'items' => [
                ['variant_id' => $v1->id, 'quantity' => 1],
                ['variant_id' => $v2->id, 'quantity' => 2],
            ],
        ], $headers);

        $this->postJson('/api/v1/cart/clear', [], $headers)
            ->assertStatus(200)
            ->assertJsonCount(0, 'data.items');
    }

    // ── Apply Discount ────────────────────────────────────────────────────────

    public function test_can_apply_valid_discount_code(): void
    {
        [, $variant] = $this->makeProductWithVariant([], ['price' => 100]);
        [$customer, $token] = $this->customerWithToken();
        $headers = $this->authHeaders($token);

        $rule = PriceRule::factory()->create(['value_type' => 'percentage', 'value' => 10]);
        DiscountCode::factory()->create(['code' => 'SAVE10', 'price_rule_id' => $rule->id]);

        $this->postJson('/api/v1/cart/add', [
            'items' => [['variant_id' => $variant->id, 'quantity' => 1]],
        ], $headers);

        $this->postJson('/api/v1/cart/apply-discount', [
            'discount_code' => 'SAVE10',
        ], $headers)
            ->assertStatus(200)
            ->assertJsonPath('applied', true);
    }

    public function test_invalid_discount_code_returns_error(): void
    {
        [, $variant] = $this->makeProductWithVariant();
        [$customer, $token] = $this->customerWithToken();
        $headers = $this->authHeaders($token);

        $this->postJson('/api/v1/cart/add', [
            'items' => [['variant_id' => $variant->id, 'quantity' => 1]],
        ], $headers);

        $this->postJson('/api/v1/cart/apply-discount', [
            'discount_code' => 'FAKE999',
        ], $headers)
            ->assertStatus(422)
            ->assertJsonPath('applied', false);
    }
}
