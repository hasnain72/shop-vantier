<?php

namespace App\Observers;

use App\Events\LowStockAlert;
use App\Jobs\SyncInventoryQuantities;
use App\Models\InventoryLevel;

class InventoryLevelObserver
{
    public function saved(InventoryLevel $level): void
    {
        $variantId = $level->inventoryItem?->variant_id;
        if ($variantId) {
            SyncInventoryQuantities::dispatch($variantId);
        }

        if ($level->available < 5) {
            LowStockAlert::dispatch($level);
        }
    }
}
