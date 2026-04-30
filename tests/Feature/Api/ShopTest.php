<?php

namespace Tests\Feature\Api;

use App\Models\Product;
use Tests\TestCase;

class ShopTest extends TestCase
{
    public function test_health_endpoint_returns_ok(): void
    {
        $this->getJson('/api/v1/health')
            ->assertStatus(200)
            ->assertJson(['success' => true, 'message' => 'OK']);
    }

    public function test_shop_info_endpoint_returns_data(): void
    {
        $this->getJson('/api/v1/shop')
            ->assertStatus(200)
            ->assertJsonStructure(['data' => ['shop']]);
    }

    public function test_search_returns_matching_products(): void
    {
        Product::factory()->create(['title' => 'Leather Watch Strap', 'status' => 'active']);
        Product::factory()->create(['title' => 'NATO Nylon Strap', 'status' => 'active']);
        Product::factory()->create(['title' => 'Watch Box', 'status' => 'active']);

        $this->getJson('/api/v1/search?q=strap')
            ->assertStatus(200)
            ->assertJsonStructure(['data' => ['results']]);

        $results = $this->getJson('/api/v1/search?q=strap')->json('data.results');
        $this->assertCount(2, $results);
    }

    public function test_search_requires_query_param(): void
    {
        $this->getJson('/api/v1/search')
            ->assertStatus(422);
    }

    public function test_search_returns_empty_for_no_match(): void
    {
        Product::factory()->create(['title' => 'Watch Box', 'status' => 'active']);

        $res = $this->getJson('/api/v1/search?q=xyznotfound');
        $res->assertStatus(200);
        $this->assertEmpty($res->json('data.results'));
    }
}
