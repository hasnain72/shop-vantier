<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $limit = min((int) ($request->limit ?? 20), 250);

        $query = Product::with(['variants', 'images'])
            ->where('status', 'active');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('collection_id')) {
            $query->whereHas('collections', fn ($q) => $q->where('collections.id', $request->collection_id));
        }

        if ($request->filled('product_type')) {
            $query->where('product_type', $request->product_type);
        }

        if ($request->filled('vendor')) {
            $query->where('vendor', $request->vendor);
        }

        if ($request->filled('title')) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }

        if ($request->filled('created_at_min')) {
            $query->where('created_at', '>=', $request->created_at_min);
        }
        if ($request->filled('created_at_max')) {
            $query->where('created_at', '<=', $request->created_at_max);
        }
        if ($request->filled('updated_at_min')) {
            $query->where('updated_at', '>=', $request->updated_at_min);
        }
        if ($request->filled('updated_at_max')) {
            $query->where('updated_at', '<=', $request->updated_at_max);
        }

        $sortBy  = in_array($request->sort_by, ['title', 'created_at', 'price']) ? $request->sort_by : 'created_at';
        $sortDir = $request->sort_direction === 'asc' ? 'asc' : 'desc';

        if ($sortBy === 'price') {
            // Sort by minimum variant price
            $query->orderByRaw('(SELECT MIN(price) FROM product_variants WHERE product_id = products.id) ' . $sortDir);
        } else {
            $query->orderBy($sortBy, $sortDir);
        }

        $products = $query->paginate($limit);

        return ApiResponse::paginated($products, ProductResource::class);
    }

    public function show(string $identifier): JsonResponse
    {
        $product = Product::with(['variants', 'images', 'collections'])
            ->where(function ($q) use ($identifier) {
                is_numeric($identifier)
                    ? $q->where('id', $identifier)
                    : $q->where('slug', $identifier);
            })
            ->where('status', 'active')
            ->firstOrFail();

        return ApiResponse::success(new ProductResource($product));
    }

    public function showByHandle(string $slug): JsonResponse
    {
        $product = Product::with(['variants', 'images', 'collections'])
            ->where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();

        return ApiResponse::success(new ProductResource($product));
    }

    public function count(): JsonResponse
    {
        $count = Product::where('status', 'active')->count();

        return ApiResponse::success(['count' => $count]);
    }
}
