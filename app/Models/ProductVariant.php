<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'title',
        'sku',
        'barcode',
        'price',
        'compare_at_price',
        'cost_per_item',
        'option1',
        'option2',
        'option3',
        'weight',
        'weight_unit',
        'requires_shipping',
        'taxable',
        'fulfillment_service',
        'inventory_management',
        'inventory_policy',
        'inventory_quantity',
        'old_inventory_quantity',
        'image_id',
        'position',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'compare_at_price' => 'decimal:2',
        'cost_per_item' => 'decimal:2',
        'weight' => 'decimal:3',
        'requires_shipping' => 'boolean',
        'taxable' => 'boolean',
        'inventory_quantity' => 'integer',
        'old_inventory_quantity' => 'integer',
        'is_active' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function image()
    {
        return $this->belongsTo(ProductImage::class, 'image_id');
    }

    public function inventoryItem()
    {
        return $this->hasOne(InventoryItem::class, 'variant_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
