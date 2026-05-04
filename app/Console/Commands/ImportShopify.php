<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\InventoryItem;
use App\Models\InventoryLevel;
use App\Models\InventoryLocation;
use App\Models\Order;
use App\Models\OrderLineItem;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductType;
use App\Models\ProductVariant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ImportShopify extends Command
{
    protected $signature = 'import:shopify
                                {--path=          : Absolute path to folder containing the CSV files}
                                {--skip-products  : Skip products import}
                                {--skip-orders    : Skip orders import}
                                {--skip-inventory : Skip inventory import}
                                {--fresh          : Truncate existing data before import}
                                {--force          : Skip confirmation prompts}';

    protected $description = 'Import Shopify CSV exports (products, orders, inventory)';

    private string $productsFile  = 'products_export.csv';
    private string $ordersFile    = 'orders_export.csv';
    private string $inventoryFile = 'inventory_export.csv';

    /** Collected stats returned as array (used by web controller) */
    public array $stats = [];

    public function handle(): int
    {
        $path = rtrim($this->option('path') ?: base_path('doc/data'), '/\\');

        $this->info("Import path: {$path}");
        $this->newLine();

        if ($this->option('fresh')) {
            if (! $this->option('force') && ! $this->confirm('--fresh will delete existing products and orders. Continue?')) {
                return self::FAILURE;
            }
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            foreach ([
                'inventory_levels', 'inventory_items',
                'product_images', 'product_variants', 'products',
                'order_line_items', 'orders',
                'customer_addresses', 'customers',
            ] as $t) {
                DB::table($t)->truncate();
            }
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            $this->warn('Tables truncated.');
        }

        if (! $this->option('skip-products')) {
            $this->importProducts("{$path}/{$this->productsFile}");
        }

        if (! $this->option('skip-inventory')) {
            $this->importInventory("{$path}/{$this->inventoryFile}");
        }

        if (! $this->option('skip-orders')) {
            $this->importOrders("{$path}/{$this->ordersFile}");
        }

        $this->newLine();
        $this->info('Import complete.');

        return self::SUCCESS;
    }

    // ═══════════════════════════════════════════════════════════
    //  PRODUCTS
    // ═══════════════════════════════════════════════════════════
    private function importProducts(string $file): void
    {
        if (! file_exists($file)) {
            $this->error("Products file not found: {$file}");
            $this->stats['products'] = ['error' => 'File not found'];
            return;
        }

        $this->info('── Importing products …');

        $rows    = $this->readCsv($file);
        $grouped = [];

        foreach ($rows as $row) {
            $handle = trim($row['Handle'] ?? '');
            if ($handle === '') continue;
            $grouped[$handle][] = $row;
        }

        $bar     = $this->output->createProgressBar(count($grouped));
        $created = 0;
        $skipped = 0;

        // Cache ProductType IDs so we don't hit DB per row
        $productTypeCache = [];

        foreach ($grouped as $handle => $productRows) {
            $first = $productRows[0];

            if (Product::where('slug', $handle)->exists()) {
                $skipped++;
                $bar->advance();
                continue;
            }

            try {
            DB::transaction(function () use ($handle, $productRows, $first, &$created, &$productTypeCache) {

                // ── Status & published_at ──────────────────────
                $status = strtolower(trim($first['Status'] ?? ''));
                if (! in_array($status, ['active', 'draft', 'archived'])) {
                    $status = ($first['Published'] ?? 'false') === 'true' ? 'active' : 'draft';
                }
                $publishedAt = ($status === 'active' || ($first['Published'] ?? 'false') === 'true')
                    ? now()
                    : null;

                // ── has_only_default_variant ───────────────────
                // True only when the product has no real options (Option1 Name is empty or "Title")
                $o1Name = trim($first['Option1 Name'] ?? '');
                $hasRealOptions = $o1Name !== '' && strtolower($o1Name) !== 'title';
                $hasOnlyDefault = ! $hasRealOptions;

                // ── product_type / product_type_id ─────────────
                $typeStr  = trim($first['Type'] ?? '');
                $typeId   = null;
                if ($typeStr !== '') {
                    if (! isset($productTypeCache[$typeStr])) {
                        $pt = ProductType::firstOrCreate(
                            ['name' => $typeStr],
                            ['slug' => Str::slug($typeStr), 'is_active' => true]
                        );
                        $productTypeCache[$typeStr] = $pt->id;
                    }
                    $typeId = $productTypeCache[$typeStr];
                }

                // ── options JSON ───────────────────────────────
                $options = $hasRealOptions ? $this->parseOptions($productRows) : [];

                // ── Create product ─────────────────────────────
                $product = Product::create([
                    'title'                   => trim($first['Title'] ?? '') ?: $handle,
                    'slug'                    => $handle,
                    'body_html'               => $first['Body (HTML)'] ?? null ?: null,
                    'vendor'                  => trim($first['Vendor'] ?? '') ?: null,
                    'product_type'            => $typeStr ?: null,
                    'product_category'        => trim($first['Product Category'] ?? '') ?: null,
                    'product_type_id'         => $typeId,
                    'tags'                    => $this->parseTags($first['Tags'] ?? ''),
                    'status'                  => $status,
                    'published_at'            => $publishedAt,
                    'meta_title'              => trim($first['SEO Title'] ?? '') ?: null,
                    'meta_description'        => trim($first['SEO Description'] ?? '') ?: null,
                    'requires_shipping'       => true,
                    'taxable'                 => true,
                    'has_only_default_variant'=> $hasOnlyDefault,
                    'options'                 => $options,
                ]);

                // ── Step 1: Create images first so we can link variants → images ──
                $imageMap = [];   // url  => ProductImage
                $imgPos   = 1;

                foreach ($productRows as $row) {
                    $src = trim($row['Image Src'] ?? '');
                    if ($src === '' || isset($imageMap[$src])) continue;

                    $img = ProductImage::create([
                        'product_id' => $product->id,
                        'src'        => $src,
                        'alt'        => trim($row['Image Alt Text'] ?? '') ?: $product->title,
                        'position'   => isset($row['Image Position']) && $row['Image Position'] !== ''
                                        ? (int) $row['Image Position']
                                        : $imgPos,
                        'width'      => null,
                        'height'     => null,
                    ]);

                    $imageMap[$src] = $img;

                    if ($imgPos === 1) {
                        // Use query builder to bypass Spatie's slug-on-update regeneration
                        Product::where('id', $product->id)->update(['featured_image' => $src]);
                    }
                    $imgPos++;
                }

                // ── Step 2: Create variants ────────────────────
                $seenVariants = [];
                $position     = 1;

                if ($hasOnlyDefault) {
                    // Single default variant — find first row with a price
                    $defaultRow = null;
                    foreach ($productRows as $row) {
                        if (trim($row['Variant Price'] ?? '') !== '') {
                            $defaultRow = $row;
                            break;
                        }
                    }

                    if ($defaultRow) {
                        $variant = $this->createVariant($product, $defaultRow, 'Default Title', null, null, null, 1);
                        InventoryItem::firstOrCreate(
                            ['variant_id' => $variant->id],
                            ['sku' => $variant->sku, 'tracked' => true, 'requires_shipping' => $variant->requires_shipping]
                        );
                    }
                } else {
                    foreach ($productRows as $row) {
                        // Build dedup key from option values
                        $o1 = trim($row['Option1 Value'] ?? '');
                        $o2 = trim($row['Option2 Value'] ?? '');
                        $o3 = trim($row['Option3 Value'] ?? '');

                        // Skip rows that have no option data and no price (pure image-only rows)
                        $variantKey = "{$o1}|{$o2}|{$o3}";
                        if ($variantKey === '||' && trim($row['Variant Price'] ?? '') === '') continue;

                        // Skip duplicate option combinations
                        if (isset($seenVariants[$variantKey])) continue;

                        $price = trim($row['Variant Price'] ?? '');
                        if ($price === '') continue;

                        $seenVariants[$variantKey] = true;

                        $titleParts = array_filter([$o1, $o2, $o3]);
                        $varTitle   = count($titleParts) ? implode(' / ', $titleParts) : 'Default Title';

                        $variant = $this->createVariant(
                            $product, $row, $varTitle,
                            $o1 ?: null, $o2 ?: null, $o3 ?: null,
                            $position++
                        );

                        InventoryItem::firstOrCreate(
                            ['variant_id' => $variant->id],
                            ['sku' => $variant->sku, 'tracked' => true, 'requires_shipping' => $variant->requires_shipping]
                        );
                    }
                }

                // ── Step 3: Link variant images ────────────────
                // Build SKU→variant and options→variant maps for quick lookup
                if (! empty($imageMap)) {
                    $variantLookup = ProductVariant::where('product_id', $product->id)->get()
                        ->keyBy(fn ($v) => trim($v->option1 . '|' . $v->option2 . '|' . $v->option3));

                    foreach ($productRows as $row) {
                        $varImgSrc = trim($row['Variant Image'] ?? '');
                        if ($varImgSrc === '' || ! isset($imageMap[$varImgSrc])) continue;

                        $o1  = trim($row['Option1 Value'] ?? '');
                        $o2  = trim($row['Option2 Value'] ?? '');
                        $o3  = trim($row['Option3 Value'] ?? '');
                        $key = "{$o1}|{$o2}|{$o3}";

                        $variant = $variantLookup->get($key);
                        if ($variant && $variant->image_id === null) {
                            $variant->update(['image_id' => $imageMap[$varImgSrc]->id]);
                        }
                    }
                }

                $created++;
            }); } catch (\Throwable $e) {
                $this->warn("  [{$handle}] failed: " . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->line("  Products: <fg=green>{$created} created</>, <fg=yellow>{$skipped} skipped</>");

        $this->stats['products'] = ['created' => $created, 'skipped' => $skipped];
    }

    // ═══════════════════════════════════════════════════════════
    //  INVENTORY  (locations + inventory_items + inventory_levels)
    // ═══════════════════════════════════════════════════════════
    private function importInventory(string $file): void
    {
        if (! file_exists($file)) {
            $this->error("Inventory file not found: {$file}");
            $this->stats['inventory'] = ['error' => 'File not found'];
            return;
        }

        $this->info('── Importing inventory …');

        $rows = $this->readCsv($file);

        $updated  = 0;
        $missed   = 0;
        $locCache = []; // name → InventoryLocation id

        foreach ($rows as $row) {
            $handle = trim($row['Handle'] ?? '');
            if ($handle === '') continue;

            $product = Product::where('slug', $handle)->first();
            if (! $product) { $missed++; continue; }

            $o1 = $row['Option1 Value'] ?? null;
            $o2 = $row['Option2 Value'] ?? null;
            $o3 = $row['Option3 Value'] ?? null;

            // Try SKU first, then option combination
            $sku     = trim($row['SKU'] ?? '');
            $variant = null;

            if ($sku !== '') {
                $variant = ProductVariant::where('sku', $sku)->first();
            }

            if (! $variant) {
                $variant = ProductVariant::where('product_id', $product->id)
                    ->where('option1', $o1)
                    ->where('option2', $o2)
                    ->where('option3', $o3)
                    ->first();
            }

            if (! $variant) { $missed++; continue; }

            // Quantity: prefer On hand (current), fallback to Available
            $onHand    = $row['On hand (current)'] ?? '';
            $available = $row['Available (not editable)'] ?? '';
            $incoming  = (int) ($row['Incoming (not editable)'] ?? 0);
            $committed = (int) ($row['Committed (not editable)'] ?? 0);

            $qty = ($onHand !== '') ? (int) $onHand
                 : (($available !== '') ? (int) $available : 0);

            // Update the denormalized qty on variant
            $variant->update(['inventory_quantity' => $qty]);

            // Find or create inventory_item
            $invItem = InventoryItem::firstOrCreate(
                ['variant_id' => $variant->id],
                [
                    'sku'              => $variant->sku,
                    'tracked'          => true,
                    'requires_shipping'=> $variant->requires_shipping,
                ]
            );

            // Find or create the location
            $locationName = trim($row['Location'] ?? '');
            if ($locationName !== '') {
                if (! isset($locCache[$locationName])) {
                    $loc = InventoryLocation::firstOrCreate(
                        ['name' => $locationName],
                        $this->buildLocationDefaults($locationName)
                    );
                    $locCache[$locationName] = $loc->id;
                }

                $locationId = $locCache[$locationName];

                // Create or update inventory_level
                InventoryLevel::updateOrCreate(
                    [
                        'inventory_item_id' => $invItem->id,
                        'location_id'       => $locationId,
                    ],
                    [
                        'available' => $qty,
                        'incoming'  => $incoming,
                        'committed' => $committed,
                    ]
                );
            }

            $updated++;
        }

        $locCount = count($locCache);
        $this->line("  Inventory: <fg=green>{$updated} variants updated</>, <fg=yellow>{$missed} not matched</>, <fg=cyan>{$locCount} location(s) ensured</>");

        $this->stats['inventory'] = [
            'updated'    => $updated,
            'missed'     => $missed,
            'locations'  => $locCount,
        ];
    }

    // ═══════════════════════════════════════════════════════════
    //  ORDERS  (+ auto-create customers & addresses)
    // ═══════════════════════════════════════════════════════════
    private function importOrders(string $file): void
    {
        if (! file_exists($file)) {
            $this->error("Orders file not found: {$file}");
            $this->stats['orders'] = ['error' => 'File not found'];
            return;
        }

        $this->info('── Importing orders …');

        $rows    = $this->readCsv($file);
        $grouped = [];

        foreach ($rows as $row) {
            $name = trim($row['Name'] ?? '');
            if ($name === '') continue;
            $grouped[$name][] = $row;
        }

        $bar             = $this->output->createProgressBar(count($grouped));
        $created         = 0;
        $skipped         = 0;
        $customerStatMap = []; // customer_id => ['count' => int, 'spent' => float]

        foreach ($grouped as $name => $orderRows) {
            $first = $orderRows[0];

            $orderNumber = ltrim($name, '#');

            if (Order::where('name', $name)->orWhere('order_number', $orderNumber)->exists()) {
                $skipped++;
                $bar->advance();
                continue;
            }

            DB::transaction(function () use ($name, $orderNumber, $orderRows, $first, &$created, &$customerStatMap) {

                // ── 1. Customer & addresses ────────────────────
                $customerId = null;
                $email      = trim($first['Email'] ?? '');

                if ($email !== '') {
                    $billingName  = trim($first['Billing Name']  ?? '');
                    $shippingName = trim($first['Shipping Name'] ?? '');
                    $displayName  = $billingName ?: $shippingName;
                    $nameParts    = $this->splitName($displayName);

                    $phone = trim($first['Phone'] ?? '')
                          ?: trim($first['Billing Phone'] ?? '')
                          ?: trim($first['Shipping Phone'] ?? '')
                          ?: null;

                    $customer = Customer::firstOrCreate(
                        ['email' => $email],
                        [
                            'first_name'        => $nameParts[0] ?: 'Unknown',
                            'last_name'         => $nameParts[1] ?: '',
                            'phone'             => $phone,
                            'password'          => Hash::make('Shopify@123'),
                            'accepts_marketing' => strtolower($first['Accepts Marketing'] ?? 'no') === 'yes',
                            'currency'          => $first['Currency'] ?? 'SAR',
                            'tags'              => $this->parseTags($first['Tags'] ?? ''),
                            'state'             => 'enabled',
                            'verified_email'    => false,
                        ]
                    );

                    // Patch phone if it was missing on an existing customer
                    if (! $customer->phone && $phone) {
                        Customer::where('id', $customer->id)->update(['phone' => $phone]);
                    }

                    $customerId = $customer->id;

                    // Shipping address (set as default)
                    $shippingAddr     = $this->buildAddress($first, 'Shipping');
                    $normShipping     = $this->normalizeAddress($shippingAddr, $nameParts, $customerId, true);
                    if (! empty($shippingAddr['address1']) && ! empty($shippingAddr['city'])) {
                        CustomerAddress::updateOrCreate(
                            ['customer_id' => $customerId, 'address1' => $normShipping['address1'], 'zip' => $normShipping['zip']],
                            $normShipping
                        );
                    }

                    // Billing address — only if different from shipping
                    $billingAddr  = $this->buildAddress($first, 'Billing');
                    $normBilling  = $this->normalizeAddress($billingAddr, $nameParts, $customerId, false);
                    $sameAddr     = $normBilling['address1'] === $normShipping['address1']
                                 && $normBilling['zip']      === $normShipping['zip'];

                    if (! $sameAddr && ! empty($billingAddr['address1']) && ! empty($billingAddr['city'])) {
                        CustomerAddress::updateOrCreate(
                            ['customer_id' => $customerId, 'address1' => $normBilling['address1'], 'zip' => $normBilling['zip']],
                            $normBilling
                        );
                    }
                }

                // ── 2. Order ───────────────────────────────────
                $shippingAddress = $this->buildAddress($first, 'Shipping');
                $billingAddress  = $this->buildAddress($first, 'Billing');

                $financialStatus = strtolower($first['Financial Status'] ?? 'pending');
                if (! in_array($financialStatus, ['pending','authorized','partially_paid','paid','partially_refunded','refunded','voided'])) {
                    $financialStatus = 'pending';
                }

                $fulfillmentStatus = strtolower($first['Fulfillment Status'] ?? '') ?: null;
                $discountCode      = trim($first['Discount Code'] ?? '');
                $discountAmount    = (float) ($first['Discount Amount'] ?? 0);

                $clientDetails = array_filter([
                    'risk_level'     => trim($first['Risk Level']    ?? '') ?: null,
                    'payment_method' => trim($first['Payment Method'] ?? '') ?: null,
                    'device_id'      => trim($first['Device ID']     ?? '') ?: null,
                    'receipt_number' => trim($first['Receipt Number'] ?? '') ?: null,
                ]);

                $noteAttributes = [];
                if (trim($first['Note Attributes'] ?? '') !== '') {
                    $noteAttributes = [['name' => 'note_attributes', 'value' => $first['Note Attributes']]];
                }

                $order = Order::create([
                    'customer_id'             => $customerId,
                    'name'                    => $name,
                    'order_number'            => $orderNumber,
                    'email'                   => $email ?: null,
                    'phone'                   => trim($first['Phone'] ?? '')
                                                 ?: trim($first['Billing Phone'] ?? '')
                                                 ?: trim($first['Shipping Phone'] ?? '')
                                                 ?: null,
                    'financial_status'        => $financialStatus,
                    'fulfillment_status'      => $fulfillmentStatus,
                    'currency'                => $first['Currency'] ?? 'SAR',
                    'subtotal_price'          => (float) ($first['Subtotal'] ?? 0),
                    'total_discounts'         => $discountAmount,
                    'total_tax'               => (float) ($first['Taxes'] ?? 0),
                    'total_shipping'          => (float) ($first['Shipping'] ?? 0),
                    'total_price'             => (float) ($first['Total'] ?? 0),
                    'discount_codes'          => $discountCode ? [['code' => $discountCode, 'amount' => $discountAmount]] : [],
                    'note'                    => trim($first['Notes'] ?? '') ?: null,
                    'note_attributes'         => $noteAttributes,
                    'buyer_accepts_marketing' => strtolower($first['Accepts Marketing'] ?? 'no') === 'yes',
                    'shipping_address'        => $shippingAddress ?: null,
                    'billing_address'         => $billingAddress ?: null,
                    'source_name'             => trim($first['Source'] ?? '') ?: 'shopify',
                    'source_identifier'       => trim($first['Id'] ?? '') ?: null,
                    'client_details'          => $clientDetails ?: null,
                    'confirmed'               => true,
                    'tags'                    => $this->parseTags($first['Tags'] ?? ''),
                    'processed_at'            => $this->parseDate($first['Paid at'] ?? null)
                                                 ?? $this->parseDate($first['Created at'] ?? null),
                    'cancelled_at'            => $this->parseDate($first['Cancelled at'] ?? null),
                    'created_at'              => $this->parseDate($first['Created at'] ?? null) ?? now(),
                    'updated_at'              => now(),
                ]);

                // ── 3. Line items ──────────────────────────────
                foreach ($orderRows as $row) {
                    $itemName = trim($row['Lineitem name'] ?? '');
                    if ($itemName === '') continue;

                    $sku     = trim($row['Lineitem sku'] ?? '');
                    $variant = $sku ? ProductVariant::where('sku', $sku)->first() : null;

                    OrderLineItem::create([
                        'order_id'             => $order->id,
                        'variant_id'           => $variant?->id,
                        'product_id'           => $variant?->product_id,
                        'title'                => $this->extractProductTitle($itemName),
                        'variant_title'        => $this->extractVariantTitle($itemName),
                        'sku'                  => $sku ?: null,
                        'vendor'               => trim($row['Vendor'] ?? '') ?: null,
                        'quantity'             => (int) ($row['Lineitem quantity'] ?? 1),
                        'price'                => (float) ($row['Lineitem price'] ?? 0),
                        'total_discount'       => (float) ($row['Lineitem discount'] ?? 0),
                        'requires_shipping'    => ($row['Lineitem requires shipping'] ?? 'true') === 'true',
                        'taxable'              => ($row['Lineitem taxable'] ?? 'true') === 'true',
                        'fulfillment_status'   => trim($row['Lineitem fulfillment status'] ?? '') ?: null,
                        'name'                 => $itemName,
                        'properties'           => [],
                        'tax_lines'            => $this->buildTaxLines($row),
                        'discount_allocations' => [],
                    ]);
                }

                // ── 4. Track customer stats ────────────────────
                if ($customerId) {
                    if (! isset($customerStatMap[$customerId])) {
                        $customerStatMap[$customerId] = ['count' => 0, 'spent' => 0.0];
                    }
                    $customerStatMap[$customerId]['count']++;
                    $customerStatMap[$customerId]['spent'] += (float) ($first['Total'] ?? 0);
                }

                $created++;
            });

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        // ── 5. Recalculate customer order stats from DB (idempotent) ─
        foreach (array_keys($customerStatMap) as $cid) {
            $totals = Order::where('customer_id', $cid)
                ->selectRaw('COUNT(*) as cnt, COALESCE(SUM(total_price), 0) as spent')
                ->first();
            Customer::where('id', $cid)->update([
                'orders_count' => $totals->cnt,
                'total_spent'  => $totals->spent,
            ]);
        }

        $custCount = count($customerStatMap);
        $this->line("  Orders: <fg=green>{$created} created</>, <fg=yellow>{$skipped} skipped</>, <fg=cyan>{$custCount} customer(s) synced</>");

        $this->stats['orders'] = ['created' => $created, 'skipped' => $skipped, 'customers' => $custCount];
    }

    /**
     * Merge address fields with fallback defaults for non-nullable CustomerAddress columns.
     */
    private function normalizeAddress(array $addr, array $nameParts, int $customerId, bool $isDefault): array
    {
        return [
            'customer_id'  => $customerId,
            'first_name'   => ($addr['first_name']  ?? '') ?: ($nameParts[0] ?: 'Unknown'),
            'last_name'    => ($addr['last_name']   ?? '') ?: ($nameParts[1] ?: ''),
            'company'      => $addr['company']      ?? null,
            'address1'     => ($addr['address1']    ?? '') ?: '-',
            'address2'     => $addr['address2']     ?? null,
            'city'         => ($addr['city']        ?? '') ?: '-',
            'province'     => $addr['province']     ?? null,
            'province_code'=> $addr['province']     ?? null,
            'country'      => ($addr['country']     ?? '') ?: 'SA',
            'country_code' => ($addr['country_code'] ?? $addr['country'] ?? '') ?: 'SA',
            'zip'          => ($addr['zip']         ?? '') ?: '00000',
            'phone'        => $addr['phone']        ?? null,
            'is_default'   => $isDefault,
        ];
    }

    // ═══════════════════════════════════════════════════════════
    //  HELPERS
    // ═══════════════════════════════════════════════════════════

    private function readCsv(string $file): array
    {
        $handle  = fopen($file, 'r');
        $headers = fgetcsv($handle);
        $rows    = [];

        while (($data = fgetcsv($handle)) !== false) {
            if (count($data) === count($headers)) {
                $rows[] = array_combine($headers, $data);
            }
        }

        fclose($handle);
        return $rows;
    }

    private function parseTags(string $tags): array
    {
        if (trim($tags) === '') return [];
        return array_map('trim', explode(',', $tags));
    }

    private function parseOptions(array $rows): array
    {
        $options = [];
        $first   = $rows[0];

        foreach ([1, 2, 3] as $n) {
            $name = trim($first["Option{$n} Name"] ?? '');
            if ($name === '' || strtolower($name) === 'title') continue;

            $values = array_unique(array_filter(
                array_map(fn ($r) => trim($r["Option{$n} Value"] ?? ''), $rows)
            ));

            if (empty($values)) continue;
            $options[] = ['name' => $name, 'values' => array_values($values)];
        }

        return $options;
    }

    private function createVariant(
        Product $product,
        array   $row,
        string  $title,
        ?string $o1,
        ?string $o2,
        ?string $o3,
        int     $position
    ): ProductVariant {
        $weightGrams = (float) ($row['Variant Grams'] ?? 0);
        $weightUnit  = strtolower(trim($row['Variant Weight Unit'] ?? 'g'));
        $weight      = $weightUnit === 'g' ? round($weightGrams / 1000, 4) : $weightGrams;
        $finalUnit   = $weightUnit === 'g' ? 'kg' : $weightUnit;

        return ProductVariant::create([
            'product_id'            => $product->id,
            'title'                 => $title,
            'sku'                   => trim($row['Variant SKU'] ?? '') ?: null,
            'barcode'               => trim($row['Variant Barcode'] ?? '') ?: null,
            'price'                 => $row['Variant Price'] !== '' ? $row['Variant Price'] : '0.00',
            'compare_at_price'      => trim($row['Variant Compare At Price'] ?? '') ?: null,
            'cost_per_item'         => trim($row['Cost per item'] ?? '') ?: null,
            'option1'               => $o1,
            'option2'               => $o2,
            'option3'               => $o3,
            'weight'                => $weight,
            'weight_unit'           => $finalUnit ?: 'kg',
            'requires_shipping'     => ($row['Variant Requires Shipping'] ?? 'true') !== 'false',
            'taxable'               => ($row['Variant Taxable'] ?? 'true') !== 'false',
            'inventory_management'  => trim($row['Variant Inventory Tracker'] ?? '') ?: 'shopify',
            'inventory_policy'      => trim($row['Variant Inventory Policy'] ?? '') ?: 'deny',
            'inventory_quantity'    => (int) ($row['Variant Inventory Qty'] ?? 0),
            'fulfillment_service'   => trim($row['Variant Fulfillment Service'] ?? '') ?: 'manual',
            'position'              => $position,
            'is_active'             => true,
        ]);
    }

    private function buildAddress(array $row, string $prefix): array
    {
        return array_filter([
            'first_name'   => $this->splitName($row["{$prefix} Name"] ?? '')[0],
            'last_name'    => $this->splitName($row["{$prefix} Name"] ?? '')[1],
            'address1'     => $row["{$prefix} Address1"] ?? null,
            'address2'     => $row["{$prefix} Address2"] ?? null ?: null,
            'company'      => $row["{$prefix} Company"] ?? null ?: null,
            'city'         => $row["{$prefix} City"] ?? null,
            'zip'          => $row["{$prefix} Zip"] ?? null,
            'province'     => $row["{$prefix} Province Name"] ?? $row["{$prefix} Province"] ?? null,
            'country'      => $row["{$prefix} Country"] ?? null,
            'country_code' => $row["{$prefix} Country Code"] ?? $row["{$prefix} Country"] ?? null,
            'phone'        => $row["{$prefix} Phone"] ?? null ?: null,
        ]);
    }

    private function splitName(string $name): array
    {
        $parts = explode(' ', trim($name), 2);
        return [$parts[0] ?? '', $parts[1] ?? ''];
    }

    private function parseDate(?string $date): ?string
    {
        if (! $date || trim($date) === '') return null;
        try {
            return \Carbon\Carbon::parse($date)->toDateTimeString();
        } catch (\Throwable) {
            return null;
        }
    }

    private function extractProductTitle(string $name): string
    {
        return trim(explode(' - ', $name)[0]);
    }

    private function extractVariantTitle(string $name): ?string
    {
        $parts = explode(' - ', $name, 2);
        return isset($parts[1]) ? trim($parts[1]) : null;
    }

    private function buildTaxLines(array $row): array
    {
        $lines = [];
        for ($i = 1; $i <= 5; $i++) {
            $taxName  = $row["Tax {$i} Name"]  ?? '';
            $taxValue = $row["Tax {$i} Value"] ?? '';
            if ($taxName === '' || $taxValue === '') continue;
            $lines[] = ['title' => $taxName, 'price' => (float) $taxValue, 'rate' => null];
        }
        return $lines;
    }

    /**
     * Build sensible default address fields for a new location created
     * from a CSV location name (e.g. "KSA Warehouse").
     */
    private function buildLocationDefaults(string $name): array
    {
        // Map common location name keywords to countries
        $countryMap = [
            'KSA'     => ['country' => 'Saudi Arabia', 'zip' => '11564'],
            'Saudi'   => ['country' => 'Saudi Arabia', 'zip' => '11564'],
            'UAE'     => ['country' => 'United Arab Emirates', 'zip' => '00000'],
            'Dubai'   => ['country' => 'United Arab Emirates', 'zip' => '00000'],
            'UK'      => ['country' => 'United Kingdom', 'zip' => 'SW1A 1AA'],
            'US'      => ['country' => 'United States', 'zip' => '00000'],
            'USA'     => ['country' => 'United States', 'zip' => '00000'],
        ];

        $country = 'Unknown';
        $zip     = '00000';

        foreach ($countryMap as $keyword => $info) {
            if (stripos($name, $keyword) !== false) {
                $country = $info['country'];
                $zip     = $info['zip'];
                break;
            }
        }

        return [
            'address1'              => '-',
            'city'                  => '-',
            'country'               => $country,
            'zip'                   => $zip,
            'is_active'             => true,
            'is_legacy'             => false,
            'fulfills_online_orders'=> true,
        ];
    }
}
