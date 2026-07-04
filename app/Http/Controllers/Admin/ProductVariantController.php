<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductVariant\StoreVariantRequest;
use App\Http\Requests\Admin\ProductVariant\UpdateVariantRequest;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

    public function bulkPriceUpdate(Request $request, Product $product): JsonResponse
    {
        $data = $request->validate([
            'variant_ids'  => 'nullable|array',
            'variant_ids.*'=> 'integer|exists:product_variants,id',
            'price_action' => 'required|in:set_fixed,increase_percent,decrease_percent,increase_fixed,decrease_fixed',
            'price_value'  => 'required|numeric|min:0',
        ]);

        $query = $product->variants();

        if (!empty($data['variant_ids'])) {
            $query->whereIn('id', $data['variant_ids']);
        }

        $variants = $query->get();

        if ($variants->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No variants found.'], 422);
        }

        $updated = 0;

        DB::transaction(function () use ($variants, $data, &$updated) {
            foreach ($variants as $variant) {
                $cur = (float) $variant->price;

                $new = match ($data['price_action']) {
                    'set_fixed'        => (float) $data['price_value'],
                    'increase_fixed'   => $cur + (float) $data['price_value'],
                    'decrease_fixed'   => max(0, $cur - (float) $data['price_value']),
                    'increase_percent' => $cur * (1 + $data['price_value'] / 100),
                    'decrease_percent' => $cur * (1 - $data['price_value'] / 100),
                };

                $variant->update(['price' => max(0, round($new, 2))]);
                $updated++;
            }
        });

        return response()->json([
            'success'  => true,
            'message'  => "{$updated} variant(s) updated.",
            'variants' => $product->variants()->get(['id', 'title', 'price']),
        ]);
    }

    public function bulkQtyUpdate(Request $request, Product $product): JsonResponse
    {
        $data = $request->validate([
            'variant_ids'   => 'nullable|array',
            'variant_ids.*' => 'integer|exists:product_variants,id',
            'qty_action'    => 'required|in:add,set',
            'qty_value'     => 'required|integer',
        ]);

        if ($data['qty_action'] === 'set' && $data['qty_value'] < 0) {
            return response()->json(['success' => false, 'message' => 'Set value cannot be negative.'], 422);
        }

        $query = $product->variants();
        if (!empty($data['variant_ids'])) {
            $query->whereIn('id', $data['variant_ids']);
        }

        $variants = $query->get();

        if ($variants->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No variants found.'], 422);
        }

        $updated = 0;

        DB::transaction(function () use ($variants, $data, &$updated) {
            foreach ($variants as $variant) {
                $current = (int) $variant->inventory_quantity;

                $new = $data['qty_action'] === 'add'
                    ? $current + (int) $data['qty_value']
                    : (int) $data['qty_value'];

                $variant->update([
                    'old_inventory_quantity' => $current,
                    'inventory_quantity'     => max(0, $new),
                ]);
                $updated++;
            }
        });

        return response()->json([
            'success'  => true,
            'message'  => "{$updated} variant(s) updated.",
            'variants' => $product->variants()->get(['id', 'title', 'inventory_quantity']),
        ]);
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

