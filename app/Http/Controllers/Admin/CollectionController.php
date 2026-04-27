<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Collection\StoreCollectionRequest;
use App\Http\Requests\Admin\Collection\UpdateCollectionRequest;
use App\Models\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CollectionController extends Controller
{
    public function index(): View
    {
        $collections = Collection::query()
            ->latest('id')
            ->paginate(20);

        return view('admin.collections.index', compact('collections'));
    }

    public function create(): View
    {
        return view('admin.collections.create');
    }

    public function store(StoreCollectionRequest $request): RedirectResponse
    {
        $collection = Collection::create($request->validated());

        return redirect()
            ->route('admin.collections.edit', $collection)
            ->with('success', 'Collection created.');
    }

    public function edit(Collection $collection): View
    {
        return view('admin.collections.edit', compact('collection'));
    }

    public function update(UpdateCollectionRequest $request, Collection $collection): RedirectResponse
    {
        $collection->update($request->validated());

        return back()->with('success', 'Collection updated.');
    }

    public function destroy(Collection $collection): RedirectResponse
    {
        $collection->delete();

        return redirect()
            ->route('admin.collections.index')
            ->with('success', 'Collection deleted.');
    }
}

