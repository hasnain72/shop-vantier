<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderFulfillment;
use App\Models\OrderLineItem;
use App\Models\ProductVariant;
use App\Models\Transaction;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    private int $orderNumber = 1001;

    public function run(): void
    {
        $ahmed  = Customer::where('email', 'ahmed.khan@example.com')->first();
        $sara   = Customer::where('email', 'sara.ali@example.com')->first();
        $james  = Customer::where('email', 'james.wilson@example.com')->first();

        // Variants to use
        $v1 = ProductVariant::where('sku', 'CLW-BK-42')->first();  // Classic Watch Black 42mm
        $v2 = ProductVariant::where('sku', 'CLW-BR-38')->first();  // Classic Watch Brown 38mm
        $v3 = ProductVariant::where('sku', 'MMS-silver-20')->first() // Mesh strap Silver 20mm
            ?? ProductVariant::whereRaw("sku LIKE 'MMS-%'")->first();
        $v4 = ProductVariant::where('sku', 'WSB-6-BLK')->first();  // Watch Storage Box
        $v5 = ProductVariant::whereRaw("sku LIKE 'SSB-%'")->first();

        if (!$v1 || !$ahmed) return; // Products not seeded yet

        // ─── Order 1: Paid + Fulfilled (Ahmed) ──────────────────────────────────
        $order1 = $this->createOrder($ahmed, [
            'financial_status'   => 'paid',
            'fulfillment_status' => 'fulfilled',
            'processed_at'       => now()->subDays(20),
            'shipping_address'   => $this->addressFor($ahmed),
        ], [
            [$v1, 1],
            [$v4, 1],
        ]);

        if ($order1->wasRecentlyCreated && $order1->exists) {
            Transaction::create([
                'order_id'     => $order1->id,
                'kind'         => 'sale',
                'gateway'      => 'bank_transfer',
                'status'       => 'success',
                'amount'       => $order1->total_price,
                'currency'     => 'USD',
                'processed_at' => now()->subDays(20),
            ]);
            OrderFulfillment::create([
                'order_id'         => $order1->id,
                'status'           => 'success',
                'tracking_company' => 'TCS',
                'tracking_number'  => 'TCS' . rand(100000, 999999),
                'shipment_status'  => 'delivered',
                'notify_customer'  => true,
                'line_items'       => [],
            ]);
        }

        // ─── Order 2: Paid + Unfulfilled (Ahmed) ────────────────────────────────
        $order2 = $this->createOrder($ahmed, [
            'financial_status'   => 'paid',
            'fulfillment_status' => null,
            'processed_at'       => now()->subDays(5),
            'shipping_address'   => $this->addressFor($ahmed),
        ], [
            [$v2, 1],
            [$v3 ?? $v1, 1],
        ]);

        if ($order2->wasRecentlyCreated && $order2->exists) {
            Transaction::create([
                'order_id'     => $order2->id,
                'kind'         => 'sale',
                'gateway'      => 'cod',
                'status'       => 'success',
                'amount'       => $order2->total_price,
                'currency'     => 'USD',
                'processed_at' => now()->subDays(5),
            ]);
        }

        // ─── Order 3: Pending payment (Sara) ────────────────────────────────────
        $order3 = $this->createOrder($sara, [
            'financial_status'   => 'pending',
            'fulfillment_status' => null,
            'shipping_address'   => $this->addressFor($sara),
        ], [
            [$v4, 1],
        ]);

        if ($order3->wasRecentlyCreated && $order3->exists) {
            Transaction::create([
                'order_id'     => $order3->id,
                'kind'         => 'sale',
                'gateway'      => 'bank_transfer',
                'status'       => 'pending',
                'amount'       => $order3->total_price,
                'currency'     => 'USD',
                'processed_at' => now()->subDays(2),
            ]);
        }

        // ─── Order 4: Cancelled (James) ─────────────────────────────────────────
        $order4 = $this->createOrder($james, [
            'financial_status'   => 'voided',
            'fulfillment_status' => null,
            'cancelled_at'       => now()->subDays(8),
            'cancel_reason'      => 'customer',
            'shipping_address'   => $this->addressFor($james),
        ], [
            [$v1, 2],
            [$v5 ?? $v4, 1],
        ]);

        // ─── Order 5: Refunded (James) ──────────────────────────────────────────
        $order5 = $this->createOrder($james, [
            'financial_status'   => 'refunded',
            'fulfillment_status' => 'fulfilled',
            'processed_at'       => now()->subDays(15),
            'shipping_address'   => $this->addressFor($james),
        ], [
            [$v1, 2],
        ]);

        if ($order5->wasRecentlyCreated && $order5->exists) {
            Transaction::create([
                'order_id'     => $order5->id,
                'kind'         => 'sale',
                'gateway'      => 'stripe',
                'status'       => 'success',
                'amount'       => $order5->total_price,
                'currency'     => 'USD',
                'processed_at' => now()->subDays(15),
            ]);
            Transaction::create([
                'order_id'     => $order5->id,
                'kind'         => 'refund',
                'gateway'      => 'stripe',
                'status'       => 'success',
                'amount'       => $order5->total_price,
                'currency'     => 'USD',
                'processed_at' => now()->subDays(12),
            ]);
        }

        // ─── Order 6: Open / Draft (no customer) ────────────────────────────────
        $this->createOrder(null, [
            'financial_status'   => 'pending',
            'fulfillment_status' => null,
            'email'              => 'walkin@guest.com',
            'shipping_address'   => [
                'first_name' => 'Walk',
                'last_name'  => 'In',
                'address1'   => '—',
                'city'       => 'Karachi',
                'country'    => 'Pakistan',
            ],
        ], [
            [$v3 ?? $v1, 1],
        ]);
    }

    private function createOrder(?Customer $customer, array $overrides, array $variantQtys): Order
    {
        $subtotal = 0;
        $lineData = [];

        foreach ($variantQtys as [$variant, $qty]) {
            if (!$variant) continue;
            $lineTotal = (float) $variant->price * $qty;
            $subtotal += $lineTotal;
            $lineData[] = compact('variant', 'qty', 'lineTotal');
        }

        $shipping = 5.00;
        $tax      = round($subtotal * 0.10, 2);
        $total    = $subtotal + $shipping + $tax;

        $attributes = array_merge([
            'customer_id'        => $customer?->id,
            'email'              => $customer?->email ?? $overrides['email'] ?? 'guest@example.com',
            'order_number'       => $this->orderNumber,
            'name'               => '#WAS' . $this->orderNumber,
            'currency'           => 'USD',
            'subtotal_price'     => $subtotal,
            'total_discounts'    => 0,
            'total_tax'          => $tax,
            'total_shipping'     => $shipping,
            'total_price'        => $total,
            'taxes_included'     => false,
            'confirmed'          => true,
            'source_name'        => 'web',
        ], $overrides);

        $order = Order::firstOrCreate(
            ['order_number' => $this->orderNumber],
            $attributes
        );

        $this->orderNumber++;

        if ($order->wasRecentlyCreated) {
            foreach ($lineData as $l) {
                $productTitle = $l['variant']->product->title ?? $l['variant']->title;
                $variantTitle = $l['variant']->title;

                OrderLineItem::create([
                    'order_id'           => $order->id,
                    'variant_id'         => $l['variant']->id,
                    'product_id'         => $l['variant']->product_id,
                    'title'              => $productTitle,
                    'name'               => $productTitle . ($variantTitle !== 'Default Title' ? ' - ' . $variantTitle : ''),
                    'variant_title'      => $variantTitle,
                    'sku'                => $l['variant']->sku,
                    'vendor'             => $l['variant']->product->vendor ?? '',
                    'price'              => $l['variant']->price,
                    'quantity'           => $l['qty'],
                    'total_discount'     => 0,
                    'taxable'            => true,
                    'requires_shipping'  => true,
                    'fulfillment_status' => $attributes['fulfillment_status'] ?? null,
                ]);
            }
        }

        return $order;
    }

    private function addressFor(Customer $customer): array
    {
        $addr = $customer->addresses()->first();
        if (!$addr) {
            return [
                'first_name' => $customer->first_name,
                'last_name'  => $customer->last_name,
                'country'    => 'Pakistan',
            ];
        }
        return [
            'first_name'   => $addr->first_name,
            'last_name'    => $addr->last_name,
            'address1'     => $addr->address1,
            'city'         => $addr->city,
            'province'     => $addr->province,
            'country'      => $addr->country,
            'country_code' => $addr->country_code,
            'zip'          => $addr->zip,
            'phone'        => $addr->phone,
        ];
    }
}
