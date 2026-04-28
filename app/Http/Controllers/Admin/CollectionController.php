<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Collection\StoreCollectionRequest;
use App\Http\Requests\Admin\Collection\UpdateCollectionRequest;
use App\Models\Collection;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CollectionController extends Controller
{
    public function index(): View
    {
        $collections = Collection::withCount('products')
            ->latest('id')
            ->paginate(20);

        return view('admin.collections.index', compact('collections'));
    }

    public function create(): View
    {
        $products = Product::orderBy('title')->get(['id', 'title', 'featured_image']);

        return view('admin.collections.create', compact('products'));
    }

    public function store(StoreCollectionRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $productIds = $data['product_ids'] ?? [];
        unset($data['product_ids']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('collections', 'public');
        }

        $collection = Collection::create($data);
        $collection->products()->sync($productIds);

        return redirect()
            ->route('admin.collections.edit', $collection)
            ->with('success', 'Collection created.');
    }

    public function edit(Collection $collection): View
    {
        $collection->load('products');
        $products = Product::orderBy('title')->get(['id', 'title', 'featured_image']);

        return view('admin.collections.edit', compact('collection', 'products'));
    }

    public function update(UpdateCollectionRequest $request, Collection $collection): RedirectResponse
    {
        $data = $request->validated();
        $productIds = $data['product_ids'] ?? [];
        unset($data['product_ids']);

        if ($request->hasFile('image')) {
            if ($collection->image) {
                Storage::disk('public')->delete($collection->image);
            }
            $data['image'] = $request->file('image')->store('collections', 'public');
        }

        $collection->update($data);
        $collection->products()->sync($productIds);

        return back()->with('success', 'Collection updated.');
    }

    public function destroy(Collection $collection): RedirectResponse
    {
        if ($collection->image) {
            Storage::disk('public')->delete($collection->image);
        }
        $collection->delete();

        return redirect()
            ->route('admin.collections.index')
            ->with('success', 'Collection deleted.');
    }
}

