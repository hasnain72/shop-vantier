<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Services\DiscountService;
use App\Services\ShippingService;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function __construct(
        private DiscountService $discountService,
        private ShippingService $shippingService
    ) {}

    public function validate(Request $request)
    {
        $data = $request->validate([
            'cart_token'                    => 'required|string',
            'shipping_address'              => 'required|array',
            'shipping_address.country_code' => 'required|string|size:2',
            'discount_code'                 => 'nullable|string',
        ]);

        $cart = Cart::with('items.variant')->where('token', $data['cart_token'])->first();
        if (!$cart || $cart->items->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Cart is empty or not found.'], 422);
        }

        $subtotal   = $cart->items->sum(fn ($i) => (float) $i->price * $i->quantity);
        $totalWeight= $cart->items->sum(fn ($i) => (float) ($i->variant?->weight ?? 0) * $i->quantity);
        $lineItems  = $cart->items->map(fn ($i) => ['variant_id' => $i->variant_id, 'quantity' => $i->quantity])->toArray();

        // Discount
        $discount = 0.0;
        if (!empty($data['discount_code'])) {
            $result = $this->discountService->validateCode($data['discount_code'], [
                'customer_id' => auth('customer')->id(),
                'line_items'  => $lineItems,
                'subtotal'    => $subtotal,
            ]);
            if ($result['valid']) {
                $discount = $result['discount_amount'];
            }
        }

        // Shipping
        $shippingRates = $this->shippingService->getAvailableRates(
            $data['shipping_address'],
            $lineItems,
            $subtotal,
            $totalWeight
        );
        $lowestShipping = count($shippingRates) ? min(array_column($shippingRates, 'price')) : 0;

        $tax   = round(($subtotal - $discount) * 0.10, 2);
        $total = $subtotal - $discount + $lowestShipping + $tax;

        return response()->json([
            'success' => true,
            'data'    => [
                'subtotal'                => $subtotal,
                'total_discounts'         => $discount,
                'total_shipping'          => $lowestShipping,
                'total_tax'               => $tax,
                'total'                   => max(0, $total),
                'shipping_rates_available'=> count($shippingRates) > 0,
                'shipping_rates'          => $shippingRates,
            ],
        ]);
    }
}
