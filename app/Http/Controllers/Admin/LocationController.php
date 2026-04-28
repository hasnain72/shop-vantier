<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryLevel;
use App\Models\InventoryLocation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LocationController extends Controller
{
    public function index(): View
    {
        $locations = InventoryLocation::withCount('inventoryLevels')->get();

        return view('admin.locations.index', compact('locations'));
    }

    public function create(): View
    {
        return view('admin.locations.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'address1'              => ['nullable', 'string'],
            'city'                  => ['nullable', 'string'],
            'country'               => ['nullable', 'string'],
            'zip'                   => ['nullable', 'string'],
            'phone'                 => ['nullable', 'string'],
            'is_active'             => ['sometimes', 'boolean'],
            'fulfills_online_orders' => ['sometimes', 'boolean'],
        ]);

        InventoryLocation::create($data);

        return redirect()->route('admin.locations.index')
            ->with('success', 'Location created.');
    }

    public function edit(InventoryLocation $location): View
    {
        return view('admin.locations.edit', compact('location'));
    }

    public function update(Request $request, InventoryLocation $location): RedirectResponse
    {
        $data = $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'address1'              => ['nullable', 'string'],
            'city'                  => ['nullable', 'string'],
            'country'               => ['nullable', 'string'],
            'zip'                   => ['nullable', 'string'],
            'phone'                 => ['nullable', 'string'],
            'is_active'             => ['sometimes', 'boolean'],
            'fulfills_online_orders' => ['sometimes', 'boolean'],
        ]);

        $location->update($data);

        return back()->with('success', 'Location updated.');
    }

    public function destroy(InventoryLocation $location): RedirectResponse
    {
        $hasStock = InventoryLevel::where('location_id', $location->id)
            ->where('available', '>', 0)
            ->exists();

        if ($hasStock) {
            return back()->with('error', 'Cannot delete a location that has inventory. Transfer stock first.');
        }

        $location->delete();

        return redirect()->route('admin.locations.index')
            ->with('success', 'Location deleted.');
    }
}
