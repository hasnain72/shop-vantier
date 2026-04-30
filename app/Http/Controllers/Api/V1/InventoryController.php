<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\InventoryLevel;
use App\Services\InventoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function __construct(private readonly InventoryService $inventory) {}

    public function index(Request $request): JsonResponse
    {
        $query = InventoryLevel::with(['inventoryItem.variant.product', 'location'])
            ->orderBy('available');

        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        if ($request->filled('low_stock')) {
            $query->where('available', '<=', (int) $request->low_stock);
        }

        $limit = min((int) ($request->limit ?? 50), 250);
        $levels = $query->paginate($limit);

        return ApiResponse::paginated($levels, fn ($level) => [
            'inventory_item_id' => $level->inventory_item_id,
            'location_id'       => $level->location_id,
            'location'          => $level->location?->name,
            'available'         => $level->available,
            'incoming'          => $level->incoming,
            'committed'         => $level->committed,
            'variant_id'        => $level->inventoryItem?->variant_id,
            'sku'               => $level->inventoryItem?->sku,
            'product_title'     => $level->inventoryItem?->variant?->product?->title,
            'updated_at'        => $level->updated_at,
        ]);
    }

    public function adjust(Request $request): JsonResponse
    {
        $data = $request->validate([
            'inventory_item_id' => ['required', 'exists:inventory_items,id'],
            'location_id'       => ['required', 'exists:inventory_locations,id'],
            'available_adjustment' => ['required', 'integer'],
            'reason'            => ['nullable', 'in:correction,received,return,damaged,theft,promotion,other'],
        ]);

        $level = $this->inventory->adjust(
            $data['inventory_item_id'],
            $data['location_id'],
            $data['available_adjustment'],
            $data['reason'] ?? 'correction'
        );

        return ApiResponse::success([
            'inventory_item_id' => $level->inventory_item_id,
            'location_id'       => $level->location_id,
            'available'         => $level->available,
        ], 'Inventory adjusted');
    }

    public function transfer(Request $request): JsonResponse
    {
        $data = $request->validate([
            'inventory_item_id'   => ['required', 'exists:inventory_items,id'],
            'from_location_id'    => ['required', 'exists:inventory_locations,id'],
            'to_location_id'      => ['required', 'exists:inventory_locations,id', 'different:from_location_id'],
            'quantity'            => ['required', 'integer', 'min:1'],
        ]);

        $this->inventory->transfer(
            $data['inventory_item_id'],
            $data['from_location_id'],
            $data['to_location_id'],
            $data['quantity']
        );

        return ApiResponse::success(null, 'Inventory transferred');
    }

    public function history(Request $request, int $item): JsonResponse
    {
        $inventoryItem = InventoryItem::with('variant.product')->findOrFail($item);

        $adjustments = $inventoryItem->adjustments()
            ->with(['location', 'user'])
            ->latest()
            ->paginate(50);

        return ApiResponse::paginated($adjustments, fn ($adj) => [
            'id'               => $adj->id,
            'location'         => $adj->location?->name,
            'adjustment'       => $adj->adjustment,
            'reason'           => $adj->reason,
            'available_before' => $adj->available_before,
            'available_after'  => $adj->available_after,
            'note'             => $adj->note,
            'created_at'       => $adj->created_at,
        ]);
    }
}
