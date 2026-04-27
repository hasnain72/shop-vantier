<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryAdjustment extends Model
{
    protected $fillable = [
        'inventory_item_id',
        'location_id',
        'user_id',
        'adjustment',
        'reason',
        'note',
        'available_before',
        'available_after',
    ];

    protected $casts = [
        'adjustment' => 'integer',
        'available_before' => 'integer',
        'available_after' => 'integer',
    ];

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    public function location()
    {
        return $this->belongsTo(InventoryLocation::class, 'location_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
