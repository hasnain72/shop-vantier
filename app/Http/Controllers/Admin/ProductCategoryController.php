<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductCategoryController extends Controller
{
    public function index(): View
    {
        $categories = ProductCategory::orderBy('sort_position')->orderBy('name')->get();

        return view('admin.product-categories.index', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'slug'          => 'nullable|string|max:255|unique:product_categories,slug',
            'description'   => 'nullable|string',
            'sort_position' => 'nullable|integer|min:0',
            'is_active'     => 'boolean',
            'image'         => 'nullable|image|max:2048',
        ]);

        $data['slug']      = $data['slug'] ? Str::slug($data['slug']) : Str::slug($data['name']);
        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('product-categories', 'public');
        }

        ProductCategory::create($data);

        return back()->with('success', 'Category created.');
    }

    public function update(Request $request, ProductCategory $productCategory): RedirectResponse
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'slug'          => 'nullable|string|max:255|unique:product_categories,slug,' . $productCategory->id,
            'description'   => 'nullable|string',
            'sort_position' => 'nullable|integer|min:0',
            'is_active'     => 'boolean',
            'image'         => 'nullable|image|max:2048',
        ]);

        $data['slug']      = $data['slug'] ? Str::slug($data['slug']) : Str::slug($data['name']);
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            if ($productCategory->image) {
                Storage::disk('public')->delete($productCategory->image);
            }
            $data['image'] = $request->file('image')->store('product-categories', 'public');
        }

        $productCategory->update($data);

        return back()->with('success', 'Category updated.');
    }

    public function destroy(ProductCategory $productCategory): RedirectResponse
    {
        if ($productCategory->image) {
            Storage::disk('public')->delete($productCategory->image);
        }

        $productCategory->delete();

        return back()->with('success', 'Category deleted.');
    }
}
