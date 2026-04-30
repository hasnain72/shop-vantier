<?php

namespace Tests;

use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Sanctum\Sanctum;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected function actingAsCustomer(?Customer $customer = null): Customer
    {
        $customer ??= Customer::factory()->create();
        Sanctum::actingAs($customer, ['*'], 'customer');
        return $customer;
    }

    protected function customerWithToken(?Customer $customer = null): array
    {
        $customer ??= Customer::factory()->create();
        Sanctum::actingAs($customer, ['*'], 'customer');
        return [$customer, 'test-token'];
    }

    protected function authHeaders(string $token): array
    {
        return ['Accept' => 'application/json'];
    }

    protected function makeProductWithVariant(array $productAttrs = [], array $variantAttrs = []): array
    {
        $product = Product::factory()->create(array_merge(['status' => 'active'], $productAttrs));
        $variant = ProductVariant::factory()->create(array_merge(
            ['product_id' => $product->id, 'inventory_quantity' => 10, 'inventory_policy' => 'deny'],
            $variantAttrs
        ));
        return [$product, $variant];
    }
}
