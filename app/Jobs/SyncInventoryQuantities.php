<?php

namespace App\Jobs;

use App\Models\InventoryItem;
use App\Models\InventoryLevel;
use App\Models\ProductVariant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncInventoryQuantities implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public readonly int $variantId) {}

    public function handle(): void
    {
        $item = InventoryItem::where('variant_id', $this->variantId)->first();
        if (! $item) return;

        $total = InventoryLevel::where('inventory_item_id', $item->id)->sum('available');

        ProductVariant::where('id', $this->variantId)
            ->update(['inventory_quantity' => $total]);
    }
}
