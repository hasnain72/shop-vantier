<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    protected $fillable = [
        'variant_id',
        'sku',
        'cost',
        'country_code_of_origin',
        'province_code_of_origin',
        'harmonized_system_code',
        'tracked',
        'requires_shipping',
    ];

    protected $casts = [
        'cost' => 'decimal:2',
        'tracked' => 'boolean',
        'requires_shipping' => 'boolean',
    ];

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    public function levels()
    {
        return $this->hasMany(InventoryLevel::class, 'inventory_item_id');
    }

    public function adjustments()
    {
        return $this->hasMany(InventoryAdjustment::class, 'inventory_item_id');
    }
}
