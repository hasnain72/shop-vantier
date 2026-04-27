<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryLocation extends Model
{
    protected $fillable = [
        'name',
        'address1',
        'address2',
        'city',
        'province',
        'country',
        'zip',
        'phone',
        'is_active',
        'is_legacy',
        'fulfills_online_orders',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_legacy' => 'boolean',
        'fulfills_online_orders' => 'boolean',
    ];

    public function inventoryLevels()
    {
        return $this->hasMany(InventoryLevel::class, 'location_id');
    }

    public function adjustments()
    {
        return $this->hasMany(InventoryAdjustment::class, 'location_id');
    }

    public function fulfillments()
    {
        return $this->hasMany(OrderFulfillment::class, 'location_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
