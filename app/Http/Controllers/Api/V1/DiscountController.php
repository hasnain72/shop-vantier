<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\DiscountService;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
    public function __construct(private DiscountService $discountService) {}

    public function lookup(Request $request)
    {
        $data = $request->validate([
            'code'                    => 'required|string',
            'line_items'              => 'required|array|min:1',
            'line_items.*.variant_id' => 'required|integer',
            'line_items.*.quantity'   => 'required|integer|min:1',
            'customer_id'             => 'nullable|integer',
        ]);

        $subtotal = 0;
        $variantIds = array_column($data['line_items'], 'variant_id');
        $variants = \App\Models\ProductVariant::whereIn('id', $variantIds)->get()->keyBy('id');

        foreach ($data['line_items'] as $item) {
            $variant = $variants->get($item['variant_id']);
            if ($variant) {
                $subtotal += (float) $variant->price * (int) $item['quantity'];
            }
        }

        $result = $this->discountService->validateCode($data['code'], [
            'customer_id' => $data['customer_id'] ?? null,
            'line_items'  => $data['line_items'],
            'subtotal'    => $subtotal,
        ]);

        if (!$result['valid']) {
            return response()->json(['discount_code' => null, 'error' => $result['error']], 422);
        }

        $rule = $result['price_rule'];
        $minAmount = $rule->prerequisite_subtotal_range['greater_than_or_equal_to'] ?? null;
        $minQty    = $rule->prerequisite_quantity_range['greater_than_or_equal_to'] ?? null;

        return response()->json([
            'discount_code' => [
                'code'             => strtoupper(trim($data['code'])),
                'amount'           => $result['discount_amount'],
                'value_type'       => $rule->value_type,
                'value'            => $rule->value,
                'minimum_amount'   => $minAmount ? (float) $minAmount : null,
                'minimum_quantity' => $minQty ? (int) $minQty : null,
            ],
            'error' => null,
        ]);
    }
}
