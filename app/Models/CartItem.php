<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = [
        'cart_id',
        'variant_id',
        'quantity',
        'price',
        'addon_price',
        'addon_id',
        'properties',
    ];

    protected $casts = [
        'quantity'    => 'integer',
        'price'       => 'decimal:2',
        'addon_price' => 'decimal:2',
        'properties'  => 'array',
    ];

    public function getTotalPriceAttribute(): float
    {
        return ((float) $this->price + (float) $this->addon_price) * $this->quantity;
    }

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    public function addon()
    {
        return $this->belongsTo(ProductAddon::class, 'addon_id');
    }
}
