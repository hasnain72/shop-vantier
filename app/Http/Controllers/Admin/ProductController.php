<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Product\StoreProductRequest;
use App\Http\Requests\Admin\Product\UpdateProductRequest;
use App\Models\Collection;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\ProductVariant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        $products = Product::query()
            ->withCount('variants')
            ->latest('id')
            ->paginate(20);

        return view('admin.products.index', compact('products'));
    }

    public function create(): View
    {
        $productTypes = ProductType::query()->active()->orderBy('name')->get();
        $collections = Collection::query()->orderBy('title')->get();

        return view('admin.products.create', compact('productTypes', 'collections'));
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $collectionIds = $data['collection_ids'] ?? [];
        unset($data['collection_ids']);

        DB::transaction(function () use ($data, $collectionIds, &$product) {
            $product = Product::create($data);

            if (! empty($collectionIds)) {
                $product->collections()->sync($collectionIds);
            }

            ProductVariant::create([
                'product_id' => $product->id,
                'title' => 'Default',
                'sku' => $data['default_sku'] ?? null,
                'price' => $data['default_price'],
                'requires_shipping' => (bool) ($data['requires_shipping'] ?? true),
                'taxable' => (bool) ($data['taxable'] ?? true),
                'is_active' => true,
            ]);
        });

        return redirect()
            ->route('admin.products.edit', $product)
            ->with('success', 'Product created.');
    }

    public function edit(Product $product): View
    {
        $product->load(['collections', 'variants']);

        $productTypes = ProductType::query()->active()->orderBy('name')->get();
        $collections = Collection::query()->orderBy('title')->get();

        return view('admin.products.edit', compact('product', 'productTypes', 'collections'));
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $data = $request->validated();

        $collectionIds = $data['collection_ids'] ?? [];
        unset($data['collection_ids']);

        $product->update($data);
        $product->collections()->sync($collectionIds);

        return back()->with('success', 'Product updated.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product deleted.');
    }
}

