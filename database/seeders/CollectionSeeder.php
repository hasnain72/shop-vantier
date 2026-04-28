<?php

namespace Database\Seeders;

use App\Models\Collection;
use Illuminate\Database\Seeder;

class CollectionSeeder extends Seeder
{
    public function run(): void
    {
        $collections = [
            [
                'title'       => 'Best Sellers',
                'description' => 'Our most popular watch accessories loved by customers worldwide.',
                'published'   => true,
                'published_at'=> now(),
                'sort_order'  => 'best-selling',
            ],
            [
                'title'       => 'New Arrivals',
                'description' => 'Fresh drops — the latest additions to our collection.',
                'published'   => true,
                'published_at'=> now(),
                'sort_order'  => 'created-desc',
            ],
            [
                'title'       => 'Watch Straps & Bands',
                'description' => 'Premium leather, NATO, mesh, and silicone straps for every style.',
                'published'   => true,
                'published_at'=> now(),
                'sort_order'  => 'manual',
            ],
            [
                'title'       => 'Luxury Watches',
                'description' => 'Handcrafted timepieces for the discerning collector.',
                'published'   => true,
                'published_at'=> now(),
                'sort_order'  => 'price-desc',
            ],
            [
                'title'       => 'Watch Care & Tools',
                'description' => 'Everything you need to maintain and store your watches.',
                'published'   => true,
                'published_at'=> now(),
                'sort_order'  => 'manual',
            ],
            [
                'title'       => 'Sale',
                'description' => 'Limited time deals on selected items.',
                'published'   => true,
                'published_at'=> now(),
                'sort_order'  => 'price-asc',
            ],
        ];

        foreach ($collections as $data) {
            Collection::firstOrCreate(['title' => $data['title']], $data);
        }
    }
}
