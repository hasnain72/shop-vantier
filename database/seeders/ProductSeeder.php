<?php

namespace Database\Seeders;

use App\Models\Collection;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $watchType  = ProductType::where('name', 'Watches')->first();
        $strapType  = ProductType::where('name', 'Watch Straps')->first();
        $bandType   = ProductType::where('name', 'Watch Bands')->first();
        $boxType    = ProductType::where('name', 'Watch Boxes')->first();
        $winderType = ProductType::where('name', 'Watch Winders')->first();

        $bestSellers = Collection::where('title', 'Best Sellers')->first();
        $newArrivals  = Collection::where('title', 'New Arrivals')->first();
        $strapsCol   = Collection::where('title', 'Watch Straps & Bands')->first();
        $luxuryCol   = Collection::where('title', 'Luxury Watches')->first();
        $careCol     = Collection::where('title', 'Watch Care & Tools')->first();
        $saleCol     = Collection::where('title', 'Sale')->first();

        // ─── 1. Classic Leather Watch ───────────────────────────────────────────
        $p1 = Product::firstOrCreate(['title' => 'Classic Leather Watch'], [
            'product_type_id'           => $watchType?->id,
            'body_html'                 => '<p>A timeless classic with genuine leather strap. Swiss movement, sapphire crystal, water resistant to 50m.</p>',
            'vendor'                    => 'Meridian Watches',
            'product_type'              => 'Watches',
            'tags'                      => ['watch', 'leather', 'classic', 'swiss'],
            'status'                    => 'active',
            'published_at'              => now()->subDays(30),
            'has_only_default_variant'  => false,
            'requires_shipping'         => true,
            'taxable'                   => true,
            'options'                   => [
                ['name' => 'Color', 'values' => ['Black', 'Brown']],
                ['name' => 'Case Size', 'values' => ['38mm', '42mm']],
            ],
        ]);

        if ($p1->wasRecentlyCreated) {
            $variants = [
                ['Black', '38mm', 'CLW-BK-38', 189.99, 145.00, 25],
                ['Black', '42mm', 'CLW-BK-42', 199.99, 150.00, 18],
                ['Brown', '38mm', 'CLW-BR-38', 189.99, 145.00, 20],
                ['Brown', '42mm', 'CLW-BR-42', 199.99, 150.00, 15],
            ];
            foreach ($variants as $i => [$c, $s, $sku, $price, $cost, $qty]) {
                ProductVariant::create([
                    'product_id'          => $p1->id,
                    'title'               => "$c / $s",
                    'option1'             => $c,
                    'option2'             => $s,
                    'sku'                 => $sku,
                    'price'               => $price,
                    'cost_per_item'       => $cost,
                    'inventory_quantity'  => $qty,
                    'weight'              => 0.120,
                    'weight_unit'         => 'kg',
                    'requires_shipping'   => true,
                    'taxable'             => true,
                    'inventory_management'=> 'shopify',
                    'inventory_policy'    => 'deny',
                    'position'            => $i + 1,
                    'is_active'           => true,
                ]);
            }
            $collections = array_filter([$bestSellers?->id, $luxuryCol?->id, $newArrivals?->id]);
            $p1->collections()->syncWithoutDetaching($collections);
        }

        // ─── 2. Sport Silicone Band ──────────────────────────────────────────────
        $p2 = Product::firstOrCreate(['title' => 'Sport Silicone Band'], [
            'product_type_id'           => $bandType?->id,
            'body_html'                 => '<p>Durable silicone sport band. Sweat-proof, hypoallergenic, easy to clean. Compatible with most 20mm and 22mm lugs.</p>',
            'vendor'                    => 'BandCraft',
            'product_type'              => 'Watch Bands',
            'tags'                      => ['band', 'silicone', 'sport', 'waterproof'],
            'status'                    => 'active',
            'published_at'              => now()->subDays(15),
            'has_only_default_variant'  => false,
            'requires_shipping'         => true,
            'taxable'                   => true,
            'options'                   => [
                ['name' => 'Color', 'values' => ['Black', 'Blue', 'Red', 'Green']],
                ['name' => 'Width', 'values' => ['20mm', '22mm']],
            ],
        ]);

        if ($p2->wasRecentlyCreated) {
            $colors = ['Black', 'Blue', 'Red', 'Green'];
            $widths = ['20mm', '22mm'];
            $pos = 1;
            foreach ($colors as $color) {
                foreach ($widths as $width) {
                    $w = $width === '20mm' ? '20' : '22';
                    $c = strtolower(substr($color, 0, 2));
                    ProductVariant::create([
                        'product_id'          => $p2->id,
                        'title'               => "$color / $width",
                        'option1'             => $color,
                        'option2'             => $width,
                        'sku'                 => "SSB-{$c}-{$w}",
                        'price'               => 14.99,
                        'cost_per_item'       => 4.50,
                        'inventory_quantity'  => rand(10, 50),
                        'weight'              => 0.030,
                        'weight_unit'         => 'kg',
                        'requires_shipping'   => true,
                        'taxable'             => true,
                        'inventory_management'=> 'shopify',
                        'inventory_policy'    => 'deny',
                        'position'            => $pos++,
                        'is_active'           => true,
                    ]);
                }
            }
            $collections = array_filter([$strapsCol?->id, $bestSellers?->id, $saleCol?->id]);
            $p2->collections()->syncWithoutDetaching($collections);
        }

        // ─── 3. Milanese Mesh Strap ──────────────────────────────────────────────
        $p3 = Product::firstOrCreate(['title' => 'Milanese Mesh Strap'], [
            'product_type_id'           => $strapType?->id,
            'body_html'                 => '<p>Stainless steel milanese mesh strap with magnetic clasp. Adjustable to any wrist size. Available in multiple widths.</p>',
            'vendor'                    => 'SteelStrap Co.',
            'product_type'              => 'Watch Straps',
            'tags'                      => ['strap', 'mesh', 'milanese', 'stainless-steel'],
            'status'                    => 'active',
            'published_at'              => now()->subDays(10),
            'has_only_default_variant'  => false,
            'requires_shipping'         => true,
            'taxable'                   => true,
            'options'                   => [
                ['name' => 'Finish', 'values' => ['Silver', 'Gold', 'Rose Gold', 'Black']],
                ['name' => 'Width', 'values' => ['18mm', '20mm', '22mm']],
            ],
        ]);

        if ($p3->wasRecentlyCreated) {
            $finishes = ['Silver', 'Gold', 'Rose Gold', 'Black'];
            $widths   = ['18mm', '20mm', '22mm'];
            $pos = 1;
            foreach ($finishes as $finish) {
                foreach ($widths as $width) {
                    $f = strtolower(str_replace(' ', '', $finish));
                    $w = str_replace('mm', '', $width);
                    ProductVariant::create([
                        'product_id'          => $p3->id,
                        'title'               => "$finish / $width",
                        'option1'             => $finish,
                        'option2'             => $width,
                        'sku'                 => "MMS-{$f}-{$w}",
                        'price'               => 29.99,
                        'compare_at_price'    => 39.99,
                        'cost_per_item'       => 9.00,
                        'inventory_quantity'  => rand(5, 30),
                        'weight'              => 0.060,
                        'weight_unit'         => 'kg',
                        'requires_shipping'   => true,
                        'taxable'             => true,
                        'inventory_management'=> 'shopify',
                        'inventory_policy'    => 'deny',
                        'position'            => $pos++,
                        'is_active'           => true,
                    ]);
                }
            }
            $collections = array_filter([$strapsCol?->id, $newArrivals?->id]);
            $p3->collections()->syncWithoutDetaching($collections);
        }

        // ─── 4. Watch Storage Box ────────────────────────────────────────────────
        $p4 = Product::firstOrCreate(['title' => 'Luxury Watch Storage Box'], [
            'product_type_id'           => $boxType?->id,
            'body_html'                 => '<p>Elegant 6-slot watch box with glass lid, velvet interior, and lock. Perfect gift for watch enthusiasts.</p>',
            'vendor'                    => 'WatchHome',
            'product_type'              => 'Watch Boxes',
            'tags'                      => ['box', 'storage', 'gift', 'luxury'],
            'status'                    => 'active',
            'published_at'              => now()->subDays(45),
            'has_only_default_variant'  => true,
            'requires_shipping'         => true,
            'taxable'                   => true,
            'options'                   => [['name' => 'Title', 'values' => ['Default Title']]],
        ]);

        if ($p4->wasRecentlyCreated) {
            ProductVariant::create([
                'product_id'          => $p4->id,
                'title'               => 'Default Title',
                'sku'                 => 'WSB-6-BLK',
                'price'               => 49.99,
                'compare_at_price'    => 65.00,
                'cost_per_item'       => 18.00,
                'inventory_quantity'  => 40,
                'weight'              => 0.800,
                'weight_unit'         => 'kg',
                'requires_shipping'   => true,
                'taxable'             => true,
                'inventory_management'=> 'shopify',
                'inventory_policy'    => 'deny',
                'position'            => 1,
                'is_active'           => true,
            ]);
            $collections = array_filter([$careCol?->id, $bestSellers?->id]);
            $p4->collections()->syncWithoutDetaching($collections);
        }

        // ─── 5. Single-Watch Automatic Winder ───────────────────────────────────
        $p5 = Product::firstOrCreate(['title' => 'Automatic Watch Winder'], [
            'product_type_id'           => $winderType?->id,
            'body_html'                 => '<p>Whisper-quiet single-slot automatic watch winder. Programmable rotation settings (CW, CCW, bi-directional). Mains or USB powered.</p>',
            'vendor'                    => 'WindMaster',
            'product_type'              => 'Watch Winders',
            'tags'                      => ['winder', 'automatic', 'electric', 'storage'],
            'status'                    => 'active',
            'published_at'              => now()->subDays(20),
            'has_only_default_variant'  => false,
            'requires_shipping'         => true,
            'taxable'                   => true,
            'options'                   => [['name' => 'Color', 'values' => ['Black', 'White', 'Walnut']]],
        ]);

        if ($p5->wasRecentlyCreated) {
            foreach ([['Black', 'AWW-BK', 89.99], ['White', 'AWW-WH', 89.99], ['Walnut', 'AWW-WN', 109.99]] as $i => [$color, $sku, $price]) {
                ProductVariant::create([
                    'product_id'          => $p5->id,
                    'title'               => $color,
                    'option1'             => $color,
                    'sku'                 => $sku,
                    'price'               => $price,
                    'cost_per_item'       => 32.00,
                    'inventory_quantity'  => rand(8, 25),
                    'weight'              => 0.650,
                    'weight_unit'         => 'kg',
                    'requires_shipping'   => true,
                    'taxable'             => true,
                    'inventory_management'=> 'shopify',
                    'inventory_policy'    => 'deny',
                    'position'            => $i + 1,
                    'is_active'           => true,
                ]);
            }
            $collections = array_filter([$careCol?->id, $newArrivals?->id]);
            $p5->collections()->syncWithoutDetaching($collections);
        }

        // ─── 6. NATO Nylon Strap ─────────────────────────────────────────────────
        $p6 = Product::firstOrCreate(['title' => 'NATO Nylon Strap'], [
            'product_type_id'           => $strapType?->id,
            'body_html'                 => '<p>Classic 2-piece NATO nylon strap. Military-grade nylon, stainless steel hardware. Available in 8 colors, 2 widths.</p>',
            'vendor'                    => 'StrapWorks',
            'product_type'              => 'Watch Straps',
            'tags'                      => ['strap', 'nato', 'nylon', 'military'],
            'status'                    => 'active',
            'published_at'              => now()->subDays(5),
            'has_only_default_variant'  => false,
            'requires_shipping'         => true,
            'taxable'                   => true,
            'options'                   => [
                ['name' => 'Color', 'values' => ['Black', 'Olive', 'Navy', 'Grey']],
                ['name' => 'Width', 'values' => ['18mm', '20mm', '22mm']],
            ],
        ]);

        if ($p6->wasRecentlyCreated) {
            $colors = ['Black', 'Olive', 'Navy', 'Grey'];
            $widths = ['18mm', '20mm', '22mm'];
            $pos = 1;
            foreach ($colors as $color) {
                foreach ($widths as $width) {
                    $c = strtolower(substr($color, 0, 2));
                    $w = str_replace('mm', '', $width);
                    ProductVariant::create([
                        'product_id'          => $p6->id,
                        'title'               => "$color / $width",
                        'option1'             => $color,
                        'option2'             => $width,
                        'sku'                 => "NATO-{$c}-{$w}",
                        'price'               => 9.99,
                        'cost_per_item'       => 2.50,
                        'inventory_quantity'  => rand(15, 60),
                        'weight'              => 0.020,
                        'weight_unit'         => 'kg',
                        'requires_shipping'   => true,
                        'taxable'             => true,
                        'inventory_management'=> 'shopify',
                        'inventory_policy'    => 'continue',
                        'position'            => $pos++,
                        'is_active'           => true,
                    ]);
                }
            }
            $collections = array_filter([$strapsCol?->id, $saleCol?->id, $bestSellers?->id]);
            $p6->collections()->syncWithoutDetaching($collections);
        }
    }
}
