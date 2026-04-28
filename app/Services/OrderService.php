<?php

namespace App\Services;

use App\Models\DiscountCode;
use App\Models\Order;
use App\Models\OrderFulfillment;
use App\Models\OrderLineItem;
use App\Models\OrderRefund;
use App\Models\ProductVariant;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    public function __construct(protected InventoryService $inventory) {}

    public function validateInventory(array $lineItems): bool
    {
        foreach ($lineItems as $item) {
            $variant = ProductVariant::find($item['variant_id']);
            if (! $variant) return false;
            if ($variant->inventory_policy === 'deny' &&
                $variant->inventory_quantity < $item['quantity']) {
                return false;
            }
        }
        return true;
    }

    public function calculateOrderTotals(array $lineItems, ?string $discountCode, array $shippingAddress): array
    {
        $subtotal = 0;
        $items    = [];

        foreach ($lineItems as $item) {
            $variant = ProductVariant::with('product')->findOrFail($item['variant_id']);
            $price   = (float) $variant->price;
            $qty     = (int) $item['quantity'];
            $subtotal += $price * $qty;
            $items[]  = compact('variant', 'price', 'qty');
        }

        $discount = 0;
        $priceRule = null;
        if ($discountCode) {
            $dc = DiscountCode::where('code', strtoupper($discountCode))->with('priceRule')->first();
            if ($dc && $dc->priceRule) {
                $priceRule = $dc->priceRule;
                $discount  = $this->applyPriceRule($priceRule, $subtotal);
            }
        }

        $tax      = round(($subtotal - $discount) * 0.1, 2); // flat 10% example
        $shipping = 0;
        $total    = max(0, $subtotal - $discount + $tax + $shipping);

        return compact('subtotal', 'discount', 'tax', 'shipping', 'total', 'items', 'priceRule', 'discountCode');
    }

    public function createOrder(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            $totals = $this->calculateOrderTotals(
                $data['line_items'],
                $data['discount_code'] ?? null,
                $data['shipping_address'] ?? []
            );

            $orderNumber = 'ORD-' . strtoupper(Str::random(8));

            $order = Order::create([
                'customer_id'          => $data['customer_id'] ?? null,
                'order_number'         => $orderNumber,
                'name'                 => '#' . $orderNumber,
                'email'                => $data['email'] ?? null,
                'phone'                => $data['phone'] ?? null,
                'financial_status'     => 'pending',
                'fulfillment_status'   => null,
                'currency'             => 'USD',
                'subtotal_price'       => $totals['subtotal'],
                'total_discounts'      => $totals['discount'],
                'total_tax'            => $totals['tax'],
                'total_shipping'       => $totals['shipping'],
                'total_price'          => $totals['total'],
                'discount_codes'       => $totals['discountCode'] ? [['code' => $totals['discountCode'], 'amount' => $totals['discount']]] : null,
                'shipping_address'     => $data['shipping_address'] ?? null,
                'billing_address'      => $data['billing_address'] ?? $data['shipping_address'] ?? null,
                'note'                 => $data['note'] ?? null,
                'buyer_accepts_marketing' => (bool) ($data['buyer_accepts_marketing'] ?? false),
                'confirmed'            => true,
                'processed_at'         => now(),
            ]);

            foreach ($totals['items'] as $item) {
                OrderLineItem::create([
                    'order_id'          => $order->id,
                    'variant_id'        => $item['variant']->id,
                    'product_id'        => $item['variant']->product_id,
                    'title'             => $item['variant']->product->title,
                    'variant_title'     => $item['variant']->title,
                    'sku'               => $item['variant']->sku,
                    'quantity'          => $item['qty'],
                    'price'             => $item['price'],
                    'requires_shipping' => $item['variant']->requires_shipping,
                    'taxable'           => $item['variant']->taxable,
                    'name'              => $item['variant']->product->title . ' - ' . $item['variant']->title,
                    'fulfillment_service' => 'manual',
                ]);
            }

            return $order->fresh(['lineItems', 'customer']);
        });
    }

    public function cancelOrder(Order $order, string $reason, bool $restock): void
    {
        DB::transaction(function () use ($order, $reason, $restock) {
            $order->update([
                'cancelled_at'     => now(),
                'cancel_reason'    => $reason,
                'financial_status' => in_array($order->financial_status, ['paid', 'partially_paid'])
                    ? 'refunded' : 'voided',
            ]);

            if ($restock) {
                foreach ($order->lineItems as $item) {
                    if ($item->variant_id) {
                        ProductVariant::where('id', $item->variant_id)
                            ->increment('inventory_quantity', $item->quantity);
                    }
                }
            }
        });
    }

    public function fulfillOrder(Order $order, array $data): OrderFulfillment
    {
        return DB::transaction(function () use ($order, $data) {
            $fulfillment = OrderFulfillment::create([
                'order_id'          => $order->id,
                'location_id'       => $data['location_id'] ?? null,
                'status'            => 'success',
                'tracking_company'  => $data['tracking_company'] ?? null,
                'tracking_number'   => $data['tracking_number'] ?? null,
                'tracking_url'      => $data['tracking_url'] ?? null,
                'notify_customer'   => (bool) ($data['notify_customer'] ?? true),
            ]);

            // Deduct inventory
            foreach ($order->lineItems as $item) {
                if ($item->variant_id) {
                    ProductVariant::where('id', $item->variant_id)
                        ->decrement('inventory_quantity', $item->quantity);
                }
            }

            $allFulfilled = $order->lineItems->every(fn ($li) => $li->fulfillment_status === 'fulfilled');
            $order->update([
                'fulfillment_status' => $allFulfilled ? 'fulfilled' : 'partial',
            ]);

            return $fulfillment;
        });
    }

    public function applyDiscount(Order $order, string $code): void
    {
        $dc = DiscountCode::where('code', strtoupper($code))->with('priceRule')->firstOrFail();
        $pr = $dc->priceRule;
        $discount = $this->applyPriceRule($pr, $order->subtotal_price);

        $order->update([
            'total_discounts' => $discount,
            'total_price'     => max(0, $order->subtotal_price - $discount + $order->total_tax + $order->total_shipping),
            'discount_codes'  => [['code' => $code, 'amount' => $discount]],
        ]);

        $dc->increment('usage_count');
        $pr->increment('usage_count');
    }

    private function applyPriceRule($priceRule, float $subtotal): float
    {
        $value = abs((float) $priceRule->value);
        if ($priceRule->value_type === 'percentage') {
            return round($subtotal * $value / 100, 2);
        }
        return min($value, $subtotal);
    }
}
