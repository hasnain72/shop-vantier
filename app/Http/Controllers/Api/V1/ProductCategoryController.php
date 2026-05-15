<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductCategoryResource;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
    /**
     * GET /v1/product-categories
     * All active categories with product counts.
     */
    public function index(): JsonResponse
    {
        $categories = ProductCategory::active()
            ->orderBy('sort_position')
            ->orderBy('name')
            ->get()
            ->map(function ($cat) {
                $cat->product_count = Product::where('product_category', $cat->slug)
                    ->where('status', 'active')
                    ->count();
                return $cat;
            });

        return ApiResponse::success([
            'categories' => ProductCategoryResource::collection($categories),
        ]);
    }

    /**
     * GET /v1/product-categories/{slug}
     * Single category detail.
     */
    public function show(string $slug): JsonResponse
    {
        $category = ProductCategory::active()->where('slug', $slug)->firstOrFail();

        $category->product_count = Product::where('product_category', $slug)
            ->where('status', 'active')
            ->count();

        return ApiResponse::success([
            'category' => new ProductCategoryResource($category),
        ]);
    }

    /**
     * GET /v1/product-categories/{slug}/products
     * Products belonging to this category (paginated).
     */
    public function products(Request $request, string $slug): JsonResponse
    {
        $category = ProductCategory::active()->where('slug', $slug)->firstOrFail();

        $limit = min((int) ($request->limit ?? 20), 250);

        $query = Product::with(['variants', 'productImages', 'addons' => fn ($q) => $q->active()])
            ->where('status', 'active')
            ->where('product_category', $slug);

        $sortBy  = in_array($request->sort_by, ['title', 'created_at', 'price']) ? $request->sort_by : 'created_at';
        $sortDir = $request->sort_direction === 'asc' ? 'asc' : 'desc';

        if ($sortBy === 'price') {
            $query->orderByRaw('(SELECT MIN(price) FROM product_variants WHERE product_id = products.id) ' . $sortDir);
        } else {
            $query->orderBy($sortBy, $sortDir);
        }

        $products = $query->paginate($limit);

        return response()->json([
            'success'  => true,
            'data'     => [
                'category' => new ProductCategoryResource($category),
                'products' => ProductResource::collection($products->items()),
            ],
            'meta' => [
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
