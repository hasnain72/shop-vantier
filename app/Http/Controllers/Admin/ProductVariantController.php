<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductVariant\StoreVariantRequest;
use App\Http\Requests\Admin\ProductVariant\UpdateVariantRequest;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProductVariantController extends Controller
{
    public function index(Product $product): View
    {
        $product->load('variants');

        return view('admin.products.variants.index', compact('product'));
    }

    public function create(Product $product): View
    {
        return view('admin.products.variants.create', compact('product'));
    }

    public function store(StoreVariantRequest $request, Product $product): RedirectResponse
    {
        $variant = new ProductVariant($request->validated());
        $variant->product()->associate($product);
        $variant->save();

        return redirect()
            ->route('admin.products.variants.index', $product)
            ->with('success', 'Variant created.');
    }

    public function edit(Product $product, ProductVariant $variant): View
    {
        abort_unless($variant->product_id === $product->id, 404);

        return view('admin.products.variants.edit', compact('product', 'variant'));
    }

    public function update(UpdateVariantRequest $request, Product $product, ProductVariant $variant): RedirectResponse
    {
        abort_unless($variant->product_id === $product->id, 404);

        $variant->update($request->validated());

        return back()->with('success', 'Variant updated.');
    }

    public function destroy(Product $product, ProductVariant $variant): RedirectResponse
    {
        abort_unless($variant->product_id === $product->id, 404);

        // Keep at least one variant for a product.
        if ($product->variants()->count() <= 1) {
            return back()->with('error', 'A product must have at least one variant.');
        }

        $variant->delete();

        return redirect()
            ->route('admin.products.variants.index', $product)
            ->with('success', 'Variant deleted.');
    }
}

