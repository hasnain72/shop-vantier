<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\InventoryLevel;
use App\Models\InventoryLocation;
use App\Services\InventoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function __construct(protected InventoryService $service) {}

    public function index(Request $request): View
    {
        $locations = InventoryLocation::where('is_active', true)->get();
        $activeLocationId = $request->location_id ?? $locations->first()?->id;

        $query = InventoryLevel::with([
            'inventoryItem.variant.product',
            'location',
        ]);

        if ($activeLocationId) {
            $query->where('location_id', $activeLocationId);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('inventoryItem.variant', fn ($q) =>
                $q->where('sku', 'like', "%$s%")
                  ->orWhereHas('product', fn ($p) => $p->where('title', 'like', "%$s%"))
            );
        }

        if ($request->filter === 'low_stock') {
            $query->where('available', '<', 5)->where('available', '>', 0);
        } elseif ($request->filter === 'out_of_stock') {
            $query->where('available', '<=', 0);
        }

        $levels = $query->paginate(50)->withQueryString();

        return view('admin.inventory.index', compact('levels', 'locations', 'activeLocationId'));
    }

    public function adjust(Request $request): JsonResponse
    {
        $data = $request->validate([
            'inventory_item_id'    => ['required', 'exists:inventory_items,id'],
            'location_id'          => ['required', 'exists:inventory_locations,id'],
            'available_adjustment' => ['required', 'integer'],
            'reason'               => ['sometimes', 'in:correction,received,return,damaged,theft,promotion,other'],
        ]);

        $level = $this->service->adjustInventory(
            $data['inventory_item_id'],
            $data['location_id'],
            $data['available_adjustment'],
            $data['reason'] ?? 'correction',
            auth()->id()
        );

        return response()->json([
            'success'   => true,
            'available' => $level->available,
        ]);
    }

    public function transfer(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'inventory_item_id' => ['required', 'exists:inventory_items,id'],
            'from_location_id'  => ['required', 'exists:inventory_locations,id'],
            'to_location_id'    => ['required', 'exists:inventory_locations,id', 'different:from_location_id'],
            'quantity'          => ['required', 'integer', 'min:1'],
        ]);

        $this->service->transferInventory(
            $data['inventory_item_id'],
            $data['from_location_id'],
            $data['to_location_id'],
            $data['quantity']
        );

        return back()->with('success', 'Inventory transferred.');
    }

    public function history(Request $request, InventoryItem $item): View
    {
        $history = $this->service->getInventoryHistory($item->id);

        return view('admin.inventory.history', compact('item', 'history'));
    }
}
