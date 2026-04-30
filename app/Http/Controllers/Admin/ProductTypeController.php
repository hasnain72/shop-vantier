<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductTypeController extends Controller
{
    public function index()
    {
        $productTypes = ProductType::withCount('products')->orderBy('name')->get();
        return view('admin.product-types.index', compact('productTypes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100|unique:product_types,name',
            'description' => 'nullable|string|max:500',
            'is_active'   => 'boolean',
        ]);

        ProductType::create([
            'name'        => $data['name'],
            'slug'        => Str::slug($data['name']),
            'description' => $data['description'] ?? null,
            'is_active'   => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'Product type created.');
    }

    public function update(Request $request, ProductType $productType)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100|unique:product_types,name,' . $productType->id,
            'description' => 'nullable|string|max:500',
            'is_active'   => 'boolean',
        ]);

        $productType->update([
            'name'        => $data['name'],
            'slug'        => Str::slug($data['name']),
            'description' => $data['description'] ?? null,
            'is_active'   => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'Product type updated.');
    }

    public function destroy(ProductType $productType)
    {
        if ($productType->products()->exists()) {
            return back()->with('error', 'Cannot delete: products are assigned to this type.');
        }

        $productType->delete();
        return back()->with('success', 'Product type deleted.');
    }
}
