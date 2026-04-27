<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderLineItem extends Model
{
    protected $fillable = [
        'order_id',
        'variant_id',
        'product_id',
        'title',
        'variant_title',
        'sku',
        'vendor',
        'quantity',
        'price',
        'total_discount',
        'tax_lines',
        'discount_allocations',
        'requires_shipping',
        'taxable',
        'gift_card',
        'name',
        'fulfillment_service',
        'fulfillment_status',
        'properties',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2',
        'total_discount' => 'decimal:2',
        'tax_lines' => 'array',
        'discount_allocations' => 'array',
        'requires_shipping' => 'boolean',
        'taxable' => 'boolean',
        'gift_card' => 'boolean',
        'properties' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }
}
