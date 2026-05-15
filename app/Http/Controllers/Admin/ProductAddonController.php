<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductAddon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductAddonController extends Controller
{
    public function index(Product $product): JsonResponse
    {
        $addons = $product->addons()->get();

        return response()->json(['success' => true, 'data' => $addons]);
    }

    public function store(Request $request, Product $product): JsonResponse
    {
        $data = $request->validate([
            'name'               => 'required|string|max:255',
            'sku'                => 'nullable|string|max:100',
            'price'              => 'required|numeric|min:0',
            'cost_per_item'      => 'nullable|numeric|min:0',
            'inventory_quantity' => 'required|integer|min:0',
            'inventory_policy'   => 'required|in:deny,continue',
            'is_default'         => 'boolean',
            'is_active'          => 'boolean',
            'position'           => 'nullable|integer|min:0',
            'image'              => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store("addons/{$product->id}", 'public');
        }

        if (!empty($data['is_default'])) {
            $product->addons()->where('is_default', true)->update(['is_default' => false]);
        }

        $data['position'] = $data['position'] ?? ($product->addons()->max('position') + 1);

        $addon = $product->addons()->create($data);

        return response()->json(['success' => true, 'message' => 'Addon created.', 'data' => $addon], 201);
    }

    public function update(Request $request, Product $product, ProductAddon $addon): JsonResponse
    {
        abort_if($addon->product_id !== $product->id, 404);

        $data = $request->validate([
            'name'               => 'required|string|max:255',
            'sku'                => 'nullable|string|max:100',
            'price'              => 'required|numeric|min:0',
            'cost_per_item'      => 'nullable|numeric|min:0',
            'inventory_quantity' => 'required|integer|min:0',
            'inventory_policy'   => 'required|in:deny,continue',
            'is_default'         => 'boolean',
            'is_active'          => 'boolean',
            'position'           => 'nullable|integer|min:0',
            'image'              => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($addon->image) {
                Storage::disk('public')->delete($addon->image);
            }
            $data['image'] = $request->file('image')->store("addons/{$product->id}", 'public');
        }

        if (!empty($data['is_default'])) {
            $product->addons()->where('id', '!=', $addon->id)->where('is_default', true)->update(['is_default' => false]);
        }

        $addon->update($data);

        return response()->json(['success' => true, 'message' => 'Addon updated.', 'data' => $addon->fresh()]);
    }

    public function adjustInventory(Request $request, Product $product, ProductAddon $addon): JsonResponse
    {
        abort_if($addon->product_id !== $product->id, 404);

        $data = $request->validate([
            'adjustment' => 'required|integer',
            'reason'     => 'required|in:correction,received,return,damaged,theft,other',
        ]);

        $before = $addon->inventory_quantity;
        $after  = max(0, $before + $data['adjustment']);

        $addon->update(['inventory_quantity' => $after]);

        return response()->json([
            'success'          => true,
            'message'          => 'Inventory adjusted.',
            'available_before' => $before,
            'available_after'  => $after,
        ]);
    }

    public function destroy(Product $product, ProductAddon $addon): JsonResponse
    {
        abort_if($addon->product_id !== $product->id, 404);

        if ($addon->image) {
            Storage::disk('public')->delete($addon->image);
        }

        $addon->delete();

        return response()->json(['success' => true, 'message' => 'Addon deleted.']);
    }
}
