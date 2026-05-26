<?php

namespace Database\Seeders;

use App\Models\Collection;
use App\Models\Product;
use App\Models\ProductAddon;
use App\Models\ProductType;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;

class DummyProductSeeder extends Seeder
{
    public function run(): void
    {
        // ── Resolve types ───────────────────────────────────────────────────────
        $strapType  = ProductType::where('name', 'Watch Straps')->first();
        $watchType  = ProductType::where('name', 'Watches')->first();
        $bandType   = ProductType::where('name', 'Watch Bands')->first();
        $boxType    = ProductType::where('name', 'Watch Boxes')->first();
        $toolType   = ProductType::where('name', 'Watch Tools')->first();

        // ── Resolve collections ─────────────────────────────────────────────────
        $bestSellers = Collection::where('title', 'Best Sellers')->first();
        $newArrivals = Collection::where('title', 'New Arrivals')->first();
        $strapsCol   = Collection::where('title', 'Watch Straps & Bands')->first();
        $luxuryCol   = Collection::where('title', 'Luxury Watches')->first();
        $careCol     = Collection::where('title', 'Watch Care & Tools')->first();
        $saleCol     = Collection::where('title', 'Sale')->first();

        // ═══════════════════════════════════════════════════════════════════════
        //  1. Premium Alligator-Grain Leather Strap
        //     Variants : Color × Width   |  Addons: spring bars, gold buckle
        // ═══════════════════════════════════════════════════════════════════════
        $p1 = Product::firstOrCreate(['title' => 'Premium Alligator-Grain Leather Strap'], [
            'product_type_id'          => $strapType?->id,
            'body_html'                => '<p>Hand-stitched alligator-grain calfskin strap with quick-release pins. Soft padded lining, genuine brass buckle. Available in four rich colours and three widths.</p><ul><li>Genuine calfskin leather</li><li>Quick-release spring bars included</li><li>Padded for all-day comfort</li><li>Water-resistant coating</li></ul>',
            'vendor'                   => 'Vantier Straps',
            'product_type'             => 'Watch Straps',
            'tags'                     => ['strap', 'leather', 'alligator', 'premium', 'handmade'],
            'status'                   => 'active',
            'published_at'             => now()->subDays(3),
            'has_only_default_variant' => false,
            'requires_shipping'        => true,
            'taxable'                  => true,
            'options'                  => [
                ['name' => 'Color',  'values' => ['Cognac', 'Dark Brown', 'Midnight Black', 'Tan']],
                ['name' => 'Width',  'values' => ['18mm', '20mm', '22mm']],
            ],
        ]);

        if ($p1->wasRecentlyCreated) {
            $colors = [
                'Cognac'          => ['COG', 54.99, 59.99],
                'Dark Brown'      => ['DBR', 54.99, 59.99],
                'Midnight Black'  => ['MBK', 54.99, 64.99],
                'Tan'             => ['TAN', 49.99, 59.99],
            ];
            $widths = ['18mm' => '18', '20mm' => '20', '22mm' => '22'];
            $pos = 1;
            foreach ($colors as $color => [$code, $price, $comp]) {
                foreach ($widths as $label => $w) {
                    ProductVariant::create([
                        'product_id'           => $p1->id,
                        'title'                => "$color / $label",
                        'option1'              => $color,
                        'option2'              => $label,
                        'sku'                  => "AGLS-{$code}-{$w}",
                        'price'                => $price,
                        'compare_at_price'     => $comp,
                        'cost_per_item'        => 16.00,
                        'inventory_quantity'   => rand(10, 35),
                        'weight'               => 0.045,
                        'weight_unit'          => 'kg',
                        'requires_shipping'    => true,
                        'taxable'              => true,
                        'inventory_management' => 'shopify',
                        'inventory_policy'     => 'deny',
                        'position'             => $pos++,
                        'is_active'            => true,
                    ]);
                }
            }

            // Addons
            ProductAddon::create([
                'product_id'         => $p1->id,
                'name'               => 'Extra Pair Quick-Release Spring Bars',
                'sku'                => 'AGLS-ADDON-SB',
                'price'              => 3.99,
                'cost_per_item'      => 0.80,
                'inventory_quantity' => 200,
                'inventory_policy'   => 'continue',
                'is_default'         => false,
                'is_active'          => true,
                'position'           => 1,
            ]);
            ProductAddon::create([
                'product_id'         => $p1->id,
                'name'               => 'Upgrade to Gold Buckle',
                'sku'                => 'AGLS-ADDON-GBK',
                'price'              => 12.99,
                'cost_per_item'      => 4.00,
                'inventory_quantity' => 80,
                'inventory_policy'   => 'deny',
                'is_default'         => false,
                'is_active'          => true,
                'position'           => 2,
            ]);
            ProductAddon::create([
                'product_id'         => $p1->id,
                'name'               => 'Luxury Gift Packaging',
                'sku'                => 'AGLS-ADDON-GIFT',
                'price'              => 7.99,
                'cost_per_item'      => 2.00,
                'inventory_quantity' => 999,
                'inventory_policy'   => 'continue',
                'is_default'         => false,
                'is_active'          => true,
                'position'           => 3,
            ]);

            $p1->collections()->syncWithoutDetaching(array_filter([
                $strapsCol?->id, $bestSellers?->id, $newArrivals?->id,
            ]));
        }

        // ═══════════════════════════════════════════════════════════════════════
        //  2. Diver FKM Rubber Strap
        //     Variants : Color × Width   |  Addons: deployment clasp, extension link
        // ═══════════════════════════════════════════════════════════════════════
        $p2 = Product::firstOrCreate(['title' => 'Diver FKM Rubber Strap'], [
            'product_type_id'          => $bandType?->id,
            'body_html'                => '<p>Professional-grade FKM fluoroelastomer rubber strap engineered for dive watches. 100% waterproof, UV-resistant and hypoallergenic. Curved ends fit most tool watches.</p><ul><li>FKM fluoroelastomer — superior to silicone</li><li>Curved lug ends for seamless fit</li><li>Brushed stainless steel pin buckle</li><li>Suitable for diving to 200m+</li></ul>',
            'vendor'                   => 'DeepBlue Gear',
            'product_type'             => 'Watch Bands',
            'tags'                     => ['rubber', 'dive', 'FKM', 'waterproof', 'sport'],
            'status'                   => 'active',
            'published_at'             => now()->subDays(7),
            'has_only_default_variant' => false,
            'requires_shipping'        => true,
            'taxable'                  => true,
            'options'                  => [
                ['name' => 'Color', 'values' => ['Stealth Black', 'Navy Blue', 'Signal Orange', 'Olive Drab', 'Vintage Grey']],
                ['name' => 'Width', 'values' => ['20mm', '22mm']],
            ],
        ]);

        if ($p2->wasRecentlyCreated) {
            $colors = [
                'Stealth Black'  => 'BLK',
                'Navy Blue'      => 'NVY',
                'Signal Orange'  => 'ORG',
                'Olive Drab'     => 'OLV',
                'Vintage Grey'   => 'GRY',
            ];
            $widths = ['20mm' => '20', '22mm' => '22'];
            $pos = 1;
            foreach ($colors as $color => $code) {
                foreach ($widths as $label => $w) {
                    $price = $label === '22mm' ? 24.99 : 22.99;
                    ProductVariant::create([
                        'product_id'           => $p2->id,
                        'title'                => "$color / $label",
                        'option1'              => $color,
                        'option2'              => $label,
                        'sku'                  => "FKM-{$code}-{$w}",
                        'price'                => $price,
                        'compare_at_price'     => $price + 8.00,
                        'cost_per_item'        => 7.00,
                        'inventory_quantity'   => rand(12, 45),
                        'weight'               => 0.035,
                        'weight_unit'          => 'kg',
                        'requires_shipping'    => true,
                        'taxable'              => true,
                        'inventory_management' => 'shopify',
                        'inventory_policy'     => 'deny',
                        'position'             => $pos++,
                        'is_active'            => true,
                    ]);
                }
            }

            ProductAddon::create([
                'product_id'         => $p2->id,
                'name'               => 'Folding Deployment Clasp (Steel)',
                'sku'                => 'FKM-ADDON-DEPL',
                'price'              => 18.99,
                'cost_per_item'      => 6.00,
                'inventory_quantity' => 60,
                'inventory_policy'   => 'deny',
                'is_default'         => false,
                'is_active'          => true,
                'position'           => 1,
            ]);
            ProductAddon::create([
                'product_id'         => $p2->id,
                'name'               => 'Wetsuit Extension Link (+50mm)',
                'sku'                => 'FKM-ADDON-EXT',
                'price'              => 6.99,
                'cost_per_item'      => 1.50,
                'inventory_quantity' => 150,
                'inventory_policy'   => 'continue',
                'is_default'         => false,
                'is_active'          => true,
                'position'           => 2,
            ]);

            $p2->collections()->syncWithoutDetaching(array_filter([
                $strapsCol?->id, $newArrivals?->id, $saleCol?->id,
            ]));
        }

        // ═══════════════════════════════════════════════════════════════════════
        //  3. Chronograph Field Watch
        //     Variants : Dial Color × Case Finish   |  Addons: extra bracelet, watch pillow, tool kit
        // ═══════════════════════════════════════════════════════════════════════
        $p3 = Product::firstOrCreate(['title' => 'Chronograph Field Watch'], [
            'product_type_id'          => $watchType?->id,
            'body_html'                => '<p>Military-inspired 42mm chronograph with a Japanese Miyota OS20 quartz movement. Lume-filled indices, 100m water resistance, domed sapphire crystal. The perfect everyday field watch.</p><ul><li>42mm stainless-steel case</li><li>Miyota OS20 quartz chronograph</li><li>Sapphire crystal with AR coating</li><li>100m water resistance</li><li>Quick-change 22mm strap system</li></ul>',
            'vendor'                   => 'Vantier Timepieces',
            'product_type'             => 'Watches',
            'tags'                     => ['watch', 'chronograph', 'field', 'quartz', 'sapphire'],
            'status'                   => 'active',
            'published_at'             => now()->subDays(2),
            'has_only_default_variant' => false,
            'requires_shipping'        => true,
            'taxable'                  => true,
            'options'                  => [
                ['name' => 'Dial Color',   'values' => ['Matte Black', 'Slate Blue', 'Sand Beige', 'Forest Green']],
                ['name' => 'Case Finish',  'values' => ['Brushed Steel', 'PVD Black']],
            ],
        ]);

        if ($p3->wasRecentlyCreated) {
            $dials = [
                'Matte Black'   => ['MBK', 179.99, 219.99],
                'Slate Blue'    => ['SBL', 179.99, 219.99],
                'Sand Beige'    => ['SBE', 169.99, 209.99],
                'Forest Green'  => ['FGR', 189.99, 229.99],
            ];
            $cases = ['Brushed Steel' => ['BS', 0], 'PVD Black' => ['PVD', 20]];
            $pos = 1;
            foreach ($dials as $dial => [$dc, $base, $comp]) {
                foreach ($cases as $case => [$cc, $extra]) {
                    ProductVariant::create([
                        'product_id'           => $p3->id,
                        'title'                => "$dial / $case",
                        'option1'              => $dial,
                        'option2'              => $case,
                        'sku'                  => "CFW-{$dc}-{$cc}",
                        'price'                => $base + $extra,
                        'compare_at_price'     => $comp + $extra,
                        'cost_per_item'        => 55.00,
                        'inventory_quantity'   => rand(5, 20),
                        'weight'               => 0.140,
                        'weight_unit'          => 'kg',
                        'requires_shipping'    => true,
                        'taxable'              => true,
                        'inventory_management' => 'shopify',
                        'inventory_policy'     => 'deny',
                        'position'             => $pos++,
                        'is_active'            => true,
                    ]);
                }
            }

            ProductAddon::create([
                'product_id'         => $p3->id,
                'name'               => 'Steel Bracelet (22mm)',
                'sku'                => 'CFW-ADDON-BRACE',
                'price'              => 34.99,
                'cost_per_item'      => 12.00,
                'inventory_quantity' => 30,
                'inventory_policy'   => 'deny',
                'is_default'         => false,
                'is_active'          => true,
                'position'           => 1,
            ]);
            ProductAddon::create([
                'product_id'         => $p3->id,
                'name'               => 'Leather Watch Pillow (Display Stand)',
                'sku'                => 'CFW-ADDON-PILLOW',
                'price'              => 9.99,
                'cost_per_item'      => 2.50,
                'inventory_quantity' => 100,
                'inventory_policy'   => 'continue',
                'is_default'         => false,
                'is_active'          => true,
                'position'           => 2,
            ]);
            ProductAddon::create([
                'product_id'         => $p3->id,
                'name'               => 'Watchmaker Tool Kit (Spring Bar + Case Opener)',
                'sku'                => 'CFW-ADDON-TOOL',
                'price'              => 14.99,
                'cost_per_item'      => 4.00,
                'inventory_quantity' => 75,
                'inventory_policy'   => 'deny',
                'is_default'         => false,
                'is_active'          => true,
                'position'           => 3,
            ]);
            ProductAddon::create([
                'product_id'         => $p3->id,
                'name'               => 'Gift Box & Certificate of Authenticity',
                'sku'                => 'CFW-ADDON-GIFT',
                'price'              => 12.99,
                'cost_per_item'      => 3.50,
                'inventory_quantity' => 999,
                'inventory_policy'   => 'continue',
                'is_default'         => false,
                'is_active'          => true,
                'position'           => 4,
            ]);

            $p3->collections()->syncWithoutDetaching(array_filter([
                $watchType ? null : null,
                $bestSellers?->id, $newArrivals?->id, $luxuryCol?->id,
            ]));
        }

        // ═══════════════════════════════════════════════════════════════════════
        //  4. Suede Perlon Ladder Strap
        //     Variants : Color × Width   |  Addons: polished buckle, engraving
        // ═══════════════════════════════════════════════════════════════════════
        $p4 = Product::firstOrCreate(['title' => 'Suede Perlon Ladder Strap'], [
            'product_type_id'          => $strapType?->id,
            'body_html'                => '<p>Woven perlon strap with a suede-feel finish. Breathable ladder weave keeps the strap cool and light on the wrist. Infinitely adjustable pin buckle slots.</p><ul><li>Woven perlon — ultra lightweight (8g)</li><li>Suede texture finish</li><li>Infinite micro-adjustment</li><li>Stainless steel pin buckle</li></ul>',
            'vendor'                   => 'Perlon Republic',
            'product_type'             => 'Watch Straps',
            'tags'                     => ['perlon', 'suede', 'woven', 'lightweight', 'strap'],
            'status'                   => 'active',
            'published_at'             => now()->subDays(12),
            'has_only_default_variant' => false,
            'requires_shipping'        => true,
            'taxable'                  => true,
            'options'                  => [
                ['name' => 'Color', 'values' => ['Dove Grey', 'Burgundy', 'Cobalt Blue', 'Racing Green', 'Cream White']],
                ['name' => 'Width', 'values' => ['18mm', '20mm', '22mm']],
            ],
        ]);

        if ($p4->wasRecentlyCreated) {
            $colors = [
                'Dove Grey'    => 'DGY',
                'Burgundy'     => 'BUR',
                'Cobalt Blue'  => 'CBL',
                'Racing Green' => 'RGR',
                'Cream White'  => 'CWH',
            ];
            $widths = ['18mm' => '18', '20mm' => '20', '22mm' => '22'];
            $pos = 1;
            foreach ($colors as $color => $code) {
                foreach ($widths as $label => $w) {
                    ProductVariant::create([
                        'product_id'           => $p4->id,
                        'title'                => "$color / $label",
                        'option1'              => $color,
                        'option2'              => $label,
                        'sku'                  => "SPL-{$code}-{$w}",
                        'price'                => 16.99,
                        'compare_at_price'     => 22.99,
                        'cost_per_item'        => 4.50,
                        'inventory_quantity'   => rand(20, 60),
                        'weight'               => 0.012,
                        'weight_unit'          => 'kg',
                        'requires_shipping'    => true,
                        'taxable'              => true,
                        'inventory_management' => 'shopify',
                        'inventory_policy'     => 'deny',
                        'position'             => $pos++,
                        'is_active'            => true,
                    ]);
                }
            }

            ProductAddon::create([
                'product_id'         => $p4->id,
                'name'               => 'Upgrade to Polished Rose-Gold Buckle',
                'sku'                => 'SPL-ADDON-RGBK',
                'price'              => 8.99,
                'cost_per_item'      => 2.50,
                'inventory_quantity' => 120,
                'inventory_policy'   => 'deny',
                'is_default'         => false,
                'is_active'          => true,
                'position'           => 1,
            ]);
            ProductAddon::create([
                'product_id'         => $p4->id,
                'name'               => 'Laser Engraving on Buckle (up to 20 chars)',
                'sku'                => 'SPL-ADDON-ENGR',
                'price'              => 11.99,
                'cost_per_item'      => 3.00,
                'inventory_quantity' => 999,
                'inventory_policy'   => 'continue',
                'is_default'         => false,
                'is_active'          => true,
                'position'           => 2,
            ]);

            $p4->collections()->syncWithoutDetaching(array_filter([
                $strapsCol?->id, $saleCol?->id, $bestSellers?->id,
            ]));
        }

        // ═══════════════════════════════════════════════════════════════════════
        //  5. 12-Slot Watch Roll Travel Case
        //     Variants : Material Color × Capacity   |  Addons: extra padding insert, monogram, care kit
        // ═══════════════════════════════════════════════════════════════════════
        $p5 = Product::firstOrCreate(['title' => '12-Slot Watch Roll Travel Case'], [
            'product_type_id'          => $boxType?->id,
            'body_html'                => '<p>Hand-rolled travel case in full-grain vegetable-tanned leather. Snap-closure, quilted microfibre interior, individual watch pillows. The ideal companion for taking your collection on the road.</p><ul><li>Full-grain veg-tan leather exterior</li><li>Quilted microfibre lining</li><li>Individual removable pillows</li><li>Snap brass closure</li><li>Available in 4- and 8-watch capacity</li></ul>',
            'vendor'                   => 'Vantier Cases',
            'product_type'             => 'Watch Boxes',
            'tags'                     => ['travel', 'roll', 'case', 'leather', 'portable', 'gift'],
            'status'                   => 'active',
            'published_at'             => now()->subDays(8),
            'has_only_default_variant' => false,
            'requires_shipping'        => true,
            'taxable'                  => true,
            'options'                  => [
                ['name' => 'Color',    'values' => ['Classic Black', 'Cognac Brown', 'Slate Grey', 'Forest Green']],
                ['name' => 'Capacity', 'values' => ['4 Watches', '8 Watches']],
            ],
        ]);

        if ($p5->wasRecentlyCreated) {
            $colors = [
                'Classic Black' => ['CBK', 59.99, 74.99],
                'Cognac Brown'  => ['COG', 64.99, 79.99],
                'Slate Grey'    => ['SGR', 59.99, 74.99],
                'Forest Green'  => ['FGR', 67.99, 84.99],
            ];
            $caps = ['4 Watches' => ['4W', 0], '8 Watches' => ['8W', 25]];
            $pos = 1;
            foreach ($colors as $color => [$cc, $base, $comp]) {
                foreach ($caps as $cap => [$capCode, $extra]) {
                    ProductVariant::create([
                        'product_id'           => $p5->id,
                        'title'                => "$color / $cap",
                        'option1'              => $color,
                        'option2'              => $cap,
                        'sku'                  => "WRC-{$cc}-{$capCode}",
                        'price'                => $base + $extra,
                        'compare_at_price'     => $comp + $extra,
                        'cost_per_item'        => 20.00,
                        'inventory_quantity'   => rand(6, 22),
                        'weight'               => 0.380,
                        'weight_unit'          => 'kg',
                        'requires_shipping'    => true,
                        'taxable'              => true,
                        'inventory_management' => 'shopify',
                        'inventory_policy'     => 'deny',
                        'position'             => $pos++,
                        'is_active'            => true,
                    ]);
                }
            }

            ProductAddon::create([
                'product_id'         => $p5->id,
                'name'               => 'Extra Pillow Insert Set (4 pillows)',
                'sku'                => 'WRC-ADDON-PILL',
                'price'              => 9.99,
                'cost_per_item'      => 2.00,
                'inventory_quantity' => 80,
                'inventory_policy'   => 'deny',
                'is_default'         => false,
                'is_active'          => true,
                'position'           => 1,
            ]);
            ProductAddon::create([
                'product_id'         => $p5->id,
                'name'               => 'Monogram Embossing (3 initials)',
                'sku'                => 'WRC-ADDON-MONO',
                'price'              => 14.99,
                'cost_per_item'      => 4.00,
                'inventory_quantity' => 999,
                'inventory_policy'   => 'continue',
                'is_default'         => false,
                'is_active'          => true,
                'position'           => 2,
            ]);
            ProductAddon::create([
                'product_id'         => $p5->id,
                'name'               => 'Leather Care Kit (conditioner + cloth)',
                'sku'                => 'WRC-ADDON-CARE',
                'price'              => 11.99,
                'cost_per_item'      => 3.50,
                'inventory_quantity' => 150,
                'inventory_policy'   => 'deny',
                'is_default'         => false,
                'is_active'          => true,
                'position'           => 3,
            ]);
            ProductAddon::create([
                'product_id'         => $p5->id,
                'name'               => 'Premium Gift Wrapping with Ribbon',
                'sku'                => 'WRC-ADDON-WRAP',
                'price'              => 5.99,
                'cost_per_item'      => 1.50,
                'inventory_quantity' => 999,
                'inventory_policy'   => 'continue',
                'is_default'         => false,
                'is_active'          => true,
                'position'           => 4,
            ]);

            $p5->collections()->syncWithoutDetaching(array_filter([
                $careCol?->id, $bestSellers?->id, $newArrivals?->id,
            ]));
        }

        // ═══════════════════════════════════════════════════════════════════════
        //  6. Vintage Canvas Pilot Strap
        //     Variants : Color × Width   |  Addons: riveted upgrade, military buckle, keeper ring
        // ═══════════════════════════════════════════════════════════════════════
        $p6 = Product::firstOrCreate(['title' => 'Vintage Canvas Pilot Strap'], [
            'product_type_id'          => $strapType?->id,
            'body_html'                => '<p>Inspired by WWII aviator watches, this waxed cotton canvas strap pairs beautifully with vintage dress watches and modern tool watches alike. Soft, breathable and develops character with wear.</p><ul><li>Waxed cotton canvas</li><li>Leather lining for comfort</li><li>Steel deployant or pin buckle choice</li><li>Straight end lug fit</li></ul>',
            'vendor'                   => 'Heritage StrapCo',
            'product_type'             => 'Watch Straps',
            'tags'                     => ['canvas', 'pilot', 'vintage', 'waxed', 'strap', 'military'],
            'status'                   => 'active',
            'published_at'             => now()->subDays(1),
            'has_only_default_variant' => false,
            'requires_shipping'        => true,
            'taxable'                  => true,
            'options'                  => [
                ['name' => 'Color', 'values' => ['Field Khaki', 'Jet Black', 'Desert Sand', 'Storm Grey', 'British Tan']],
                ['name' => 'Width', 'values' => ['18mm', '20mm', '22mm', '24mm']],
            ],
        ]);

        if ($p6->wasRecentlyCreated) {
            $colors = [
                'Field Khaki'  => 'FKH',
                'Jet Black'    => 'JBK',
                'Desert Sand'  => 'DSN',
                'Storm Grey'   => 'SGR',
                'British Tan'  => 'BTN',
            ];
            $widths = ['18mm' => '18', '20mm' => '20', '22mm' => '22', '24mm' => '24'];
            $pos = 1;
            foreach ($colors as $color => $code) {
                foreach ($widths as $label => $w) {
                    $price = $w >= 22 ? 21.99 : 19.99;
                    ProductVariant::create([
                        'product_id'           => $p6->id,
                        'title'                => "$color / $label",
                        'option1'              => $color,
                        'option2'              => $label,
                        'sku'                  => "VCP-{$code}-{$w}",
                        'price'                => $price,
                        'compare_at_price'     => $price + 6.00,
                        'cost_per_item'        => 6.00,
                        'inventory_quantity'   => rand(8, 40),
                        'weight'               => 0.028,
                        'weight_unit'          => 'kg',
                        'requires_shipping'    => true,
                        'taxable'              => true,
                        'inventory_management' => 'shopify',
                        'inventory_policy'     => 'deny',
                        'position'             => $pos++,
                        'is_active'            => true,
                    ]);
                }
            }

            ProductAddon::create([
                'product_id'         => $p6->id,
                'name'               => 'Riveted Stitch Upgrade (hand-stitched)',
                'sku'                => 'VCP-ADDON-RIV',
                'price'              => 9.99,
                'cost_per_item'      => 3.00,
                'inventory_quantity' => 999,
                'inventory_policy'   => 'continue',
                'is_default'         => false,
                'is_active'          => true,
                'position'           => 1,
            ]);
            ProductAddon::create([
                'product_id'         => $p6->id,
                'name'               => 'Military-Style Deployant Buckle',
                'sku'                => 'VCP-ADDON-DEPL',
                'price'              => 16.99,
                'cost_per_item'      => 5.50,
                'inventory_quantity' => 50,
                'inventory_policy'   => 'deny',
                'is_default'         => false,
                'is_active'          => true,
                'position'           => 2,
            ]);
            ProductAddon::create([
                'product_id'         => $p6->id,
                'name'               => 'Extra Leather Keeper Ring',
                'sku'                => 'VCP-ADDON-KEEP',
                'price'              => 2.99,
                'cost_per_item'      => 0.50,
                'inventory_quantity' => 500,
                'inventory_policy'   => 'continue',
                'is_default'         => false,
                'is_active'          => true,
                'position'           => 3,
            ]);

            $p6->collections()->syncWithoutDetaching(array_filter([
                $strapsCol?->id, $newArrivals?->id,
            ]));
        }

        // ═══════════════════════════════════════════════════════════════════════
        //  7. Professional Watch Cleaning & Care Kit
        //     Variants : Kit Size   |  Addons: ultrasonic cleaner, polishing cloth set
        // ═══════════════════════════════════════════════════════════════════════
        $p7 = Product::firstOrCreate(['title' => 'Professional Watch Cleaning & Care Kit'], [
            'product_type_id'          => $toolType?->id,
            'body_html'                => '<p>Everything you need to keep your watches looking showroom-fresh. pH-neutral watch cleaning solution, anti-static polishing cloths, crystal cleaner, and a soft-bristle cleaning brush.</p><ul><li>50ml pH-neutral cleaning solution</li><li>3× anti-static microfibre cloths</li><li>Crystal-safe cleaning brush</li><li>Case-back opening tool</li><li>Spring bar tool</li></ul>',
            'vendor'                   => 'ClockworkCare',
            'product_type'             => 'Watch Tools',
            'tags'                     => ['cleaning', 'care', 'tool', 'maintenance', 'polish'],
            'status'                   => 'active',
            'published_at'             => now()->subDays(14),
            'has_only_default_variant' => false,
            'requires_shipping'        => true,
            'taxable'                  => true,
            'options'                  => [
                ['name' => 'Kit',   'values' => ['Essential Kit', 'Deluxe Kit', 'Pro Kit']],
                ['name' => 'Color', 'values' => ['Black Case', 'White Case']],
            ],
        ]);

        if ($p7->wasRecentlyCreated) {
            $kits = [
                'Essential Kit' => ['ESS', 19.99, 29.99],
                'Deluxe Kit'    => ['DLX', 34.99, 44.99],
                'Pro Kit'       => ['PRO', 54.99, 69.99],
            ];
            $caseColors = ['Black Case' => 'BK', 'White Case' => 'WH'];
            $pos = 1;
            foreach ($kits as $kit => [$kc, $price, $comp]) {
                foreach ($caseColors as $caseColor => $cc) {
                    ProductVariant::create([
                        'product_id'           => $p7->id,
                        'title'                => "$kit / $caseColor",
                        'option1'              => $kit,
                        'option2'              => $caseColor,
                        'sku'                  => "WCK-{$kc}-{$cc}",
                        'price'                => $price,
                        'compare_at_price'     => $comp,
                        'cost_per_item'        => $price * 0.30,
                        'inventory_quantity'   => rand(15, 50),
                        'weight'               => 0.250,
                        'weight_unit'          => 'kg',
                        'requires_shipping'    => true,
                        'taxable'              => true,
                        'inventory_management' => 'shopify',
                        'inventory_policy'     => 'deny',
                        'position'             => $pos++,
                        'is_active'            => true,
                    ]);
                }
            }

            ProductAddon::create([
                'product_id'         => $p7->id,
                'name'               => 'Mini Ultrasonic Cleaner (600ml)',
                'sku'                => 'WCK-ADDON-US',
                'price'              => 39.99,
                'cost_per_item'      => 14.00,
                'inventory_quantity' => 25,
                'inventory_policy'   => 'deny',
                'is_default'         => false,
                'is_active'          => true,
                'position'           => 1,
            ]);
            ProductAddon::create([
                'product_id'         => $p7->id,
                'name'               => 'Deluxe Polishing Cloth Set (6 pieces)',
                'sku'                => 'WCK-ADDON-CLOTH',
                'price'              => 7.99,
                'cost_per_item'      => 1.50,
                'inventory_quantity' => 200,
                'inventory_policy'   => 'deny',
                'is_default'         => false,
                'is_active'          => true,
                'position'           => 2,
            ]);
            ProductAddon::create([
                'product_id'         => $p7->id,
                'name'               => 'Watch Case-Back Press (5-Die Set)',
                'sku'                => 'WCK-ADDON-PRESS',
                'price'              => 24.99,
                'cost_per_item'      => 8.00,
                'inventory_quantity' => 40,
                'inventory_policy'   => 'deny',
                'is_default'         => false,
                'is_active'          => true,
                'position'           => 3,
            ]);

            $p7->collections()->syncWithoutDetaching(array_filter([
                $careCol?->id, $bestSellers?->id,
            ]));
        }
    }
}
