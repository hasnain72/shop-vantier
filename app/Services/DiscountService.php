<?php

namespace App\Services;

use App\Models\DiscountCode;
use App\Models\Order;
use App\Models\PriceRule;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;

class DiscountService
{
    public function validateCode(string $code, array $context): array
    {
        $discountCode = DiscountCode::where('code', strtoupper(trim($code)))->first();

        if (!$discountCode) {
            return $this->invalid('Discount code not found.');
        }

        $rule = $discountCode->priceRule;

        // 1. Active date window
        if ($rule->starts_at && $rule->starts_at->isFuture()) {
            return $this->invalid('This discount code is not yet active.');
        }
        if ($rule->ends_at && $rule->ends_at->isPast()) {
            return $this->invalid('This discount code has expired.');
        }

        // 2. Global usage limit
        if ($rule->usage_limit && $rule->usage_count >= $rule->usage_limit) {
            return $this->invalid('This discount code has reached its usage limit.');
        }

        // 3. Per-customer usage limit
        if ($rule->once_per_customer && !empty($context['customer_id'])) {
            $used = Order::where('customer_id', $context['customer_id'])
                ->whereJsonContains('discount_codes', ['code' => strtoupper(trim($code))])
                ->exists();
            if ($used) {
                return $this->invalid('You have already used this discount code.');
            }
        }

        // 4. Minimum subtotal requirement
        $subtotalRange = $rule->prerequisite_subtotal_range;
        if (!empty($subtotalRange['greater_than_or_equal_to'])) {
            $min = (float) $subtotalRange['greater_than_or_equal_to'];
            if (($context['subtotal'] ?? 0) < $min) {
                return $this->invalid("Minimum order amount of $$min required.");
            }
        }

        // 5. Minimum quantity requirement
        $qtyRange = $rule->prerequisite_quantity_range;
        if (!empty($qtyRange['greater_than_or_equal_to'])) {
            $totalQty = array_sum(array_column($context['line_items'] ?? [], 'quantity'));
            if ($totalQty < (int) $qtyRange['greater_than_or_equal_to']) {
                return $this->invalid('Minimum item quantity not met.');
            }
        }

        // 6. Entitled products/collections filter
        $discountAmount = $this->calculateDiscount($rule, $context['line_items'] ?? [], $context['subtotal'] ?? 0);

        return [
            'valid'          => true,
            'error'          => null,
            'discount_amount'=> $discountAmount,
            'price_rule'     => $rule,
            'discount_code'  => $discountCode,
        ];
    }

    public function calculateDiscount(PriceRule $rule, array $lineItems, float $subtotal): float
    {
        $applicable = $this->getApplicableAmount($rule, $lineItems, $subtotal);

        return match ($rule->value_type) {
            'percentage'   => round(abs($rule->value) / 100 * $applicable, 2),
            'fixed_amount' => min(abs($rule->value), $applicable),
            'free_shipping'=> 0.0, // handled at shipping level
            default        => 0.0,
        };
    }

    private function getApplicableAmount(PriceRule $rule, array $lineItems, float $subtotal): float
    {
        // If specific products/variants/collections are entitled, calculate only those line totals
        if ($rule->target_selection === 'entitled') {
            $entitledVariantIds    = $rule->entitled_variant_ids ?? [];
            $entitledProductIds    = $rule->entitled_product_ids ?? [];
            $entitledCollectionIds = $rule->entitled_collection_ids ?? [];

            if (empty($entitledVariantIds) && empty($entitledProductIds) && empty($entitledCollectionIds)) {
                return $subtotal;
            }

            $variantIds = array_column($lineItems, 'variant_id');
            $variants   = ProductVariant::with('product.collections')
                ->whereIn('id', $variantIds)
                ->get()
                ->keyBy('id');

            $amount = 0.0;
            foreach ($lineItems as $item) {
                $variant = $variants->get($item['variant_id'] ?? null);
                if (!$variant) continue;

                $eligible = in_array($variant->id, $entitledVariantIds)
                    || in_array($variant->product_id, $entitledProductIds)
                    || $variant->product->collections->pluck('id')->intersect($entitledCollectionIds)->isNotEmpty();

                if ($eligible) {
                    $amount += (float) $variant->price * (int) ($item['quantity'] ?? 1);
                }
            }
            return $amount;
        }

        return $subtotal;
    }

    public function applyCodeToOrder(Order $order, string $code): void
    {
        $lineItems = $order->lineItems->map(fn ($l) => [
            'variant_id' => $l->variant_id,
            'quantity'   => $l->quantity,
        ])->toArray();

        $result = $this->validateCode($code, [
            'customer_id' => $order->customer_id,
            'line_items'  => $lineItems,
            'subtotal'    => (float) $order->subtotal_price,
        ]);

        if (!$result['valid']) {
            throw new \InvalidArgumentException($result['error']);
        }

        $codes   = $order->discount_codes ?? [];
        $codes[] = ['code' => strtoupper(trim($code)), 'amount' => $result['discount_amount'], 'type' => $result['price_rule']->value_type];

        $order->update([
            'discount_codes'  => $codes,
            'total_discounts' => $order->total_discounts + $result['discount_amount'],
            'total_price'     => max(0, $order->total_price - $result['discount_amount']),
        ]);

        $this->incrementUsage($result['discount_code'], $order->customer_id);
    }

    public function incrementUsage(DiscountCode $discountCode, ?int $customerId): void
    {
        DB::transaction(function () use ($discountCode) {
            $discountCode->increment('usage_count');
            $discountCode->priceRule->increment('usage_count');
        });
    }

    private function invalid(string $error): array
    {
        return ['valid' => false, 'error' => $error, 'discount_amount' => 0.0, 'price_rule' => null, 'discount_code' => null];
    }
}
