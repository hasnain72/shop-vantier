<?php

namespace Tests;

use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected function actingAsCustomer(?Customer $customer = null): Customer
    {
        $customer ??= Customer::factory()->create();
        $this->actingAs($customer, 'customer');
        return $customer;
    }

    protected function customerWithToken(?Customer $customer = null): array
    {
        $customer ??= Customer::factory()->create();
        $token = $customer->createToken('test', ['customer'])->plainTextToken;
        return [$customer, $token];
    }

    protected function authHeaders(string $token): array
    {
        return ['Authorization' => "Bearer {$token}", 'Accept' => 'application/json'];
    }

    protected function makeProductWithVariant(array $productAttrs = [], array $variantAttrs = []): array
    {
        $product = Product::factory()->create($productAttrs);
        $variant = ProductVariant::factory()->create(array_merge(
            ['product_id' => $product->id, 'inventory_quantity' => 10],
            $variantAttrs
        ));
        return [$product, $variant];
    }
}
