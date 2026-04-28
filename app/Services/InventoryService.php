<?php

namespace App\Services;

use App\Models\InventoryAdjustment;
use App\Models\InventoryItem;
use App\Models\InventoryLevel;
use App\Models\ProductVariant;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    public function adjustInventory(int $inventoryItemId, int $locationId, int $adjustment, string $reason, ?int $userId = null): InventoryLevel
    {
        return DB::transaction(function () use ($inventoryItemId, $locationId, $adjustment, $reason, $userId) {
            $level = InventoryLevel::firstOrCreate(
                ['inventory_item_id' => $inventoryItemId, 'location_id' => $locationId],
                ['available' => 0]
            );

            $before = $level->available;
            $after  = max(0, $before + $adjustment);

            $level->update(['available' => $after]);

            InventoryAdjustment::create([
                'inventory_item_id' => $inventoryItemId,
                'location_id'       => $locationId,
                'user_id'           => $userId,
                'adjustment'        => $adjustment,
                'reason'            => $reason,
                'available_before'  => $before,
                'available_after'   => $after,
            ]);

            $this->syncVariantQuantity($level->inventoryItem->variant);

            return $level->fresh();
        });
    }

    public function setInventory(int $inventoryItemId, int $locationId, int $available): InventoryLevel
    {
        $level = InventoryLevel::firstOrCreate(
            ['inventory_item_id' => $inventoryItemId, 'location_id' => $locationId],
            ['available' => 0]
        );

        $adjustment = $available - $level->available;
        return $this->adjustInventory($inventoryItemId, $locationId, $adjustment, 'correction');
    }

    public function transferInventory(int $itemId, int $fromLocationId, int $toLocationId, int $qty): void
    {
        DB::transaction(function () use ($itemId, $fromLocationId, $toLocationId, $qty) {
            $this->adjustInventory($itemId, $fromLocationId, -$qty, 'correction');
            $this->adjustInventory($itemId, $toLocationId,    $qty, 'received');
        });
    }

    public function getLowStockItems(int $threshold = 5): Collection
    {
        return ProductVariant::with('product')
            ->where('inventory_quantity', '<', $threshold)
            ->where('is_active', true)
            ->orderBy('inventory_quantity')
            ->get();
    }

    public function getInventoryHistory(int $inventoryItemId): Collection
    {
        return InventoryAdjustment::with(['location', 'user'])
            ->where('inventory_item_id', $inventoryItemId)
            ->latest()
            ->get();
    }

    public function syncVariantQuantity(ProductVariant $variant): void
    {
        $item = InventoryItem::where('variant_id', $variant->id)->first();
        if (! $item) return;

        $total = InventoryLevel::where('inventory_item_id', $item->id)->sum('available');
        $variant->update(['inventory_quantity' => $total]);
    }

    public function checkAvailability(int $variantId, int $quantity): bool
    {
        $variant = ProductVariant::find($variantId);
        if (! $variant) return false;
        if ($variant->inventory_policy === 'continue') return true;

        return $variant->inventory_quantity >= $quantity;
    }

    public function deductInventory(ProductVariant $variant, int $quantity, ?int $locationId = null): void
    {
        $item = InventoryItem::where('variant_id', $variant->id)->first();
        if ($item && $locationId) {
            $this->adjustInventory($item->id, $locationId, -$quantity, 'other');
        } else {
            $variant->decrement('inventory_quantity', $quantity);
        }
    }

    public function restockInventory(ProductVariant $variant, int $quantity, ?int $locationId = null): void
    {
        $item = InventoryItem::where('variant_id', $variant->id)->first();
        if ($item && $locationId) {
            $this->adjustInventory($item->id, $locationId, $quantity, 'return');
        } else {
            $variant->increment('inventory_quantity', $quantity);
        }
    }
}
