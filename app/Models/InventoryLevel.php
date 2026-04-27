<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryLevel extends Model
{
    protected $fillable = [
        'inventory_item_id',
        'location_id',
        'available',
        'incoming',
        'committed',
    ];

    protected $casts = [
        'available' => 'integer',
        'incoming' => 'integer',
        'committed' => 'integer',
    ];

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    public function location()
    {
        return $this->belongsTo(InventoryLocation::class, 'location_id');
    }
}
