<?php

namespace Tests\Feature\Api;

use App\Models\Collection;
use App\Models\Product;
use App\Models\ProductVariant;
use Tests\TestCase;

class ProductTest extends TestCase
{
    // ── Index ─────────────────────────────────────────────────────────────────

    public function test_can_list_active_products(): void
    {
        Product::factory()->count(3)->create(['status' => 'active']);
        Product::factory()->create(['status' => 'draft']); // should not appear

        $res = $this->getJson('/api/v1/products');

        $res->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonCount(3, 'data.products');
    }

    public function test_products_list_is_paginated(): void
    {
        Product::factory()->count(5)->create(['status' => 'active']);

        $res = $this->getJson('/api/v1/products?limit=2');

        $res->assertStatus(200)
            ->assertJsonCount(2, 'data.products');
    }

    public function test_can_filter_products_by_vendor(): void
    {
        Product::factory()->create(['status' => 'active', 'vendor' => 'Casio']);
        Product::factory()->create(['status' => 'active', 'vendor' => 'Seiko']);

        $this->getJson('/api/v1/products?vendor=Casio')
            ->assertStatus(200)
            ->assertJsonCount(1, 'data.products')
            ->assertJsonPath('data.products.0.vendor', 'Casio');
    }

    public function test_can_filter_products_by_collection(): void
    {
        [$product1] = $this->makeProductWithVariant();
        [$product2] = $this->makeProductWithVariant();
        $collection = Collection::factory()->create();
        $collection->products()->attach($product1->id);

        $this->getJson("/api/v1/products?collection_id={$collection->id}")
            ->assertStatus(200)
            ->assertJsonCount(1, 'data.products');
    }

    // ── Show ──────────────────────────────────────────────────────────────────

    public function test_can_get_product_by_id(): void
    {
        [$product] = $this->makeProductWithVariant();

        $this->getJson("/api/v1/products/{$product->id}")
            ->assertStatus(200)
            ->assertJsonPath('data.product.id', $product->id)
            ->assertJsonStructure(['data' => ['product' => ['id', 'title', 'variants']]]);
    }

    public function test_can_get_product_by_handle(): void
    {
        [$product] = $this->makeProductWithVariant();

        $this->getJson("/api/v1/products/handle/{$product->slug}")
            ->assertStatus(200)
            ->assertJsonPath('data.product.slug', $product->slug);
    }

    public function test_show_returns_404_for_unknown_product(): void
    {
        $this->getJson('/api/v1/products/99999')
            ->assertStatus(404);
    }

    public function test_draft_product_not_accessible_by_handle(): void
    {
        $product = Product::factory()->draft()->create();

        $this->getJson("/api/v1/products/handle/{$product->slug}")
            ->assertStatus(404);
    }

    // ── Count ─────────────────────────────────────────────────────────────────

    public function test_can_get_product_count(): void
    {
        Product::factory()->count(4)->create(['status' => 'active']);

        $this->getJson('/api/v1/products/count')
            ->assertStatus(200)
            ->assertJsonPath('data.count', 4);
    }
}
