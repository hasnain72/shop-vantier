<?php

namespace App\Events;

use App\Models\InventoryLevel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LowStockAlert
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly InventoryLevel $level) {}
}
