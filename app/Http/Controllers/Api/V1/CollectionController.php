<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\CollectionResource;
use App\Http\Resources\ProductResource;
use App\Models\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CollectionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $limit = min((int) ($request->limit ?? 20), 250);

        $query = Collection::withCount('products');

        if ($request->filled('published')) {
            $request->boolean('published')
                ? $query->where('published', true)
                : $query->where('published', false);
        } else {
            $query->where('published', true);
        }

        $collections = $query->orderBy('sort_position')->paginate($limit);

        return response()->json([
            'success' => true,
            'data'    => ['collections' => CollectionResource::collection($collections->items())],
            'meta'    => [
                'pagination' => [
                    'total'        => $collections->total(),
                    'per_page'     => $collections->perPage(),
                    'current_page' => $collections->currentPage(),
                    'last_page'    => $collections->lastPage(),
                ],
            ],
        ]);
    }

    public function show(string $identifier): JsonResponse
    {
        $collection = Collection::withCount('products')
            ->where(function ($q) use ($identifier) {
                is_numeric($identifier)
                    ? $q->where('id', $identifier)
                    : $q->where('slug', $identifier);
            })
            ->firstOrFail();

        return ApiResponse::success(['collection' => new CollectionResource($collection)]);
    }

    public function count(): JsonResponse
    {
        return ApiResponse::success(['count' => Collection::where('published', true)->count()]);
    }

    public function showByHandle(string $slug): JsonResponse
    {
        $collection = Collection::withCount('products')
            ->where('slug', $slug)->firstOrFail();
        return ApiResponse::success(['collection' => new CollectionResource($collection)]);
    }

    public function products(Request $request, string $identifier): JsonResponse
    {
        $collection = Collection::where(function ($q) use ($identifier) {
            is_numeric($identifier)
                ? $q->where('id', $identifier)
                : $q->where('slug', $identifier);
        })->firstOrFail();

        $limit = min((int) ($request->limit ?? 20), 250);

        $products = $collection->products()
            ->with(['variants', 'productImages'])
            ->where('status', 'active')
            ->paginate($limit);

        return response()->json([
            'success' => true,
            'data'    => ['products' => ProductResource::collection($products->items())],
            'meta'    => [
                'pagination' => [
                    'total'        => $products->total(),
                    'per_page'     => $products->perPage(),
                    'current_page' => $products->currentPage(),
                    'last_page'    => $products->lastPage(),
                ],
            ],
        ]);
    }
}
