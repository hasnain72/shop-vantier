<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductAddon extends Model
{
    protected $fillable = [
        'product_id',
        'name',
        'sku',
        'image',
        'price',
        'cost_per_item',
        'inventory_quantity',
        'inventory_policy',
        'is_default',
        'is_active',
        'position',
    ];

    protected $casts = [
        'price'              => 'decimal:2',
        'cost_per_item'      => 'decimal:2',
        'inventory_quantity' => 'integer',
        'is_default'         => 'boolean',
        'is_active'          => 'boolean',
        'position'           => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function hasStock(int $qty = 1): bool
    {
        if ($this->inventory_policy === 'continue') {
            return true;
        }

        return $this->inventory_quantity >= $qty;
    }

    public function decrementStock(int $qty = 1): void
    {
        if ($this->inventory_policy === 'deny') {
            $this->decrement('inventory_quantity', $qty);
        }
    }
}
