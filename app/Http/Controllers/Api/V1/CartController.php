<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductAddon;
use App\Models\ProductVariant;
use App\Services\DiscountService;
use App\Services\ShippingService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CartController extends Controller
{
    public function __construct(
        private DiscountService $discountService,
        private ShippingService $shippingService
    ) {}

    public function show(Request $request)
    {
        $cart = $this->resolveCart($request, create: false);
        if (!$cart) {
            return response()->json(['success' => false, 'message' => 'Cart not found.'], 404);
        }
        return response()->json(['success' => true, 'data' => new CartResource($cart->load(['items.variant.product', 'items.addon']))]);
    }

    public function add(Request $request)
    {
        $data = $request->validate([
            'items'                     => 'required|array|min:1',
            'items.*.variant_id'        => 'required|integer',
            'items.*.quantity'          => 'required|integer|min:1',
            'items.*.addon_id'          => 'nullable|integer|exists:product_addons,id',
            'items.*.properties'        => 'nullable|array',
        ]);

        $cart = $this->resolveCart($request, create: true);

        foreach ($data['items'] as $item) {
            $variant = ProductVariant::find($item['variant_id']);
            if (!$variant) continue;

            if ($variant->inventory_policy === 'deny' && $variant->inventory_quantity < $item['quantity']) {
                return response()->json(['success' => false, 'message' => "Insufficient stock for: {$variant->title}"], 422);
            }

            $addon      = null;
            $addonPrice = 0.00;

            if (!empty($item['addon_id'])) {
                $addon = ProductAddon::find($item['addon_id']);
                if ($addon) {
                    if (!$addon->hasStock($item['quantity'])) {
                        return response()->json(['success' => false, 'message' => "Insufficient stock for addon: {$addon->name}"], 422);
                    }
                    $addonPrice = (float) $addon->price;
                }
            }

            $existing = $cart->items()
                ->where('variant_id', $item['variant_id'])
                ->where('addon_id', $addon?->id)
                ->first();

            if ($existing) {
                $existing->increment('quantity', $item['quantity']);
            } else {
                $cart->items()->create([
                    'variant_id'  => $item['variant_id'],
                    'quantity'    => $item['quantity'],
                    'price'       => $variant->price,
                    'addon_id'    => $addon?->id,
                    'addon_price' => $addonPrice,
                    'properties'  => $item['properties'] ?? null,
                ]);
            }
        }

        $cart->update(['requires_shipping' => true]);
        $cart->load(['items.variant.product', 'items.addon']);

        return response()->json([
            'success' => true,
            'message' => 'Item(s) added to cart.',
            'data'    => new CartResource($cart),
            'cart_token' => $cart->token,
        ], 201);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'updates'   => 'required|array',
            'updates.*' => 'integer|min:0',
        ]);

        $cart = $this->resolveCart($request, create: false);
        if (!$cart) return response()->json(['success' => false, 'message' => 'Cart not found.'], 404);

        foreach ($data['updates'] as $lineItemId => $qty) {
            $item = $cart->items()->find($lineItemId);
            if (!$item) continue;
            $qty === 0 ? $item->delete() : $item->update(['quantity' => $qty]);
        }

        $cart->load(['items.variant.product', 'items.addon']);
        return response()->json(['success' => true, 'data' => new CartResource($cart)]);
    }

    public function remove(Request $request)
    {
        $data = $request->validate(['line_item_id' => 'required|integer']);
        $cart = $this->resolveCart($request, create: false);
        if (!$cart) return response()->json(['success' => false, 'message' => 'Cart not found.'], 404);

        $cart->items()->where('id', $data['line_item_id'])->delete();
        $cart->load(['items.variant.product', 'items.addon']);
        return response()->json(['success' => true, 'data' => new CartResource($cart)]);
    }

    public function clear(Request $request)
    {
        $cart = $this->resolveCart($request, create: false);
        if ($cart) {
            $cart->items()->delete();
            $cart->load(['items.variant.product', 'items.addon']);
        }

        return response()->json(['success' => true, 'message' => 'Cart cleared.', 'data' => $cart ? new CartResource($cart) : null]);
    }

    public function applyDiscount(Request $request)
    {
        $data = $request->validate(['discount_code' => 'required|string']);
        $cart = $this->resolveCart($request, create: false);
        if (!$cart) return response()->json(['success' => false, 'message' => 'Cart not found.'], 404);

        $cart->load(['items.variant.product', 'items.addon']);
        $subtotal = $cart->items->sum(fn ($i) => (float) $i->price * $i->quantity);
        $lineItems = $cart->items->map(fn ($i) => ['variant_id' => $i->variant_id, 'quantity' => $i->quantity])->toArray();

        $result = $this->discountService->validateCode($data['discount_code'], [
            'customer_id' => auth('customer')->id(),
            'line_items'  => $lineItems,
            'subtotal'    => $subtotal,
        ]);

        if (!$result['valid']) {
            return response()->json(['applied' => false, 'discount_amount' => 0, 'error' => $result['error']], 422);
        }

        $attrs = $cart->attributes ?? [];
        $attrs['discount_code'] = strtoupper(trim($data['discount_code']));
        $cart->update(['attributes' => $attrs]);

        return response()->json(['applied' => true, 'discount_amount' => $result['discount_amount'], 'error' => null]);
    }

    public function shippingRates(Request $request)
    {
        $data = $request->validate([
            'country_code'  => 'required|string|size:2',
            'province_code' => 'nullable|string',
            'zip'           => 'nullable|string',
        ]);

        $cart = $this->resolveCart($request, create: false);
        $subtotal = 0;
        $weight   = 0;

        if ($cart) {
            $cart->load('items.variant');
            $subtotal = $cart->items->sum(fn ($i) => (float) $i->price * $i->quantity);
            $weight   = $cart->items->sum(fn ($i) => (float) ($i->variant?->weight ?? 0) * $i->quantity);
        }

        $rates = $this->shippingService->getAvailableRates(
            ['country_code' => $data['country_code']],
            [],
            $subtotal,
            $weight
        );

        return response()->json(['success' => true, 'data' => ['shipping_rates' => $rates]]);
    }

    private function resolveCart(Request $request, bool $create): ?Cart
    {
        $customer = auth('customer')->user();
        $token    = $request->header('X-Cart-Token');

        $query = Cart::with(['items.variant.product', 'items.addon']);

        if ($customer) {
            $cart = $query->firstWhere('customer_id', $customer->id);
            if (!$cart && $create) {
                $cart = Cart::create([
                    'customer_id' => $customer->id,
                    'token'       => Str::uuid(),
                    'currency'    => 'USD',
                ]);
            }
            return $cart;
        }

        if ($token) {
            $cart = $query->firstWhere('token', $token);
            if (!$cart && $create) {
                $cart = Cart::create(['token' => $token, 'currency' => 'USD']);
            }
            return $cart;
        }

        if ($create) {
            return Cart::create(['token' => (string) Str::uuid(), 'currency' => 'USD']);
        }

        return null;
    }
}
