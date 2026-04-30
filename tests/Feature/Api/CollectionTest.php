<?php

namespace Tests\Feature\Api;

use App\Models\Collection;
use Tests\TestCase;

class CollectionTest extends TestCase
{
    public function test_can_list_published_collections(): void
    {
        Collection::factory()->count(3)->create(['published' => true]);
        Collection::factory()->create(['published' => false]); // hidden

        $this->getJson('/api/v1/collections')
            ->assertStatus(200)
            ->assertJsonCount(3, 'data.collections');
    }

    public function test_can_get_collection_by_id(): void
    {
        $collection = Collection::factory()->create();

        $this->getJson("/api/v1/collections/{$collection->id}")
            ->assertStatus(200)
            ->assertJsonPath('data.collection.id', $collection->id);
    }

    public function test_can_get_collection_by_handle(): void
    {
        $collection = Collection::factory()->create();

        $this->getJson("/api/v1/collections/handle/{$collection->slug}")
            ->assertStatus(200)
            ->assertJsonPath('data.collection.slug', $collection->slug);
    }

    public function test_can_get_collection_count(): void
    {
        Collection::factory()->count(2)->create(['published' => true]);

        $this->getJson('/api/v1/collections/count')
            ->assertStatus(200)
            ->assertJsonPath('data.count', 2);
    }

    public function test_can_get_products_in_collection(): void
    {
        [$product] = $this->makeProductWithVariant(['status' => 'active']);
        $collection = Collection::factory()->create();
        $collection->products()->attach($product->id);

        $this->getJson("/api/v1/collections/{$collection->id}/products")
            ->assertStatus(200)
            ->assertJsonCount(1, 'data.products');
    }

    public function test_collection_not_found_returns_404(): void
    {
        $this->getJson('/api/v1/collections/99999')
            ->assertStatus(404);
    }
}
