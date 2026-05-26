<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Wishlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $wishlist = $this->resolveWishlist($request, create: false);

        if (!$wishlist) {
            return ApiResponse::success(['products' => [], 'product_ids' => []]);
        }

        $products = $wishlist->products()
            ->with(['variants', 'productImages'])
            ->where('status', 'active')
            ->get();

        return ApiResponse::success([
            'products'    => ProductResource::collection($products),
            'product_ids' => $products->pluck('id')->map(fn ($id) => (string) $id)->values(),
        ]);
    }

    public function toggle(Request $request): JsonResponse
    {
        $data = $request->validate(['product_id' => 'required|integer|exists:products,id']);

        $wishlist  = $this->resolveWishlist($request, create: true);
        $productId = (int) $data['product_id'];
        $existing  = $wishlist->items()->where('product_id', $productId)->first();

        if ($existing) {
            $existing->delete();
            $action = 'removed';
        } else {
            $wishlist->items()->create(['product_id' => $productId]);
            $action = 'added';
        }

        $productIds = $wishlist->items()->pluck('product_id')
            ->map(fn ($id) => (string) $id)->values()->toArray();

        return ApiResponse::success([
            'action'      => $action,
            'product_ids' => $productIds,
        ]);
    }

    public function clear(Request $request): JsonResponse
    {
        $wishlist = $this->resolveWishlist($request, create: false);

        if ($wishlist) {
            $wishlist->items()->delete();
        }

        return ApiResponse::success(['product_ids' => []]);
    }

    private function resolveWishlist(Request $request, bool $create): ?Wishlist
    {
        $sessionId = $request->header('X-Wishlist-Session')
            ?? $request->query('wishlist_session');

        if (!$sessionId) {
            return null;
        }

        if ($create) {
            return Wishlist::firstOrCreate(['session_id' => $sessionId]);
        }

        return Wishlist::where('session_id', $sessionId)->first();
    }
}
