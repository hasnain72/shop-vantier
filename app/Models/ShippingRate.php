<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingRate extends Model
{
    protected $fillable = [
        'shipping_zone_id',
        'name',
        'price',
        'rate_type',
        'min_order_subtotal',
        'max_order_subtotal',
        'min_weight',
        'max_weight',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'min_order_subtotal' => 'decimal:2',
        'max_order_subtotal' => 'decimal:2',
        'min_weight' => 'decimal:3',
        'max_weight' => 'decimal:3',
        'is_active' => 'boolean',
    ];

    public function zone()
    {
        return $this->belongsTo(ShippingZone::class, 'shipping_zone_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
