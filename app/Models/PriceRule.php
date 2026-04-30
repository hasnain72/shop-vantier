<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceRule extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'target_type',
        'target_selection',
        'allocation_method',
        'value_type',
        'value',
        'customer_selection',
        'prerequisite_subtotal_range',
        'prerequisite_quantity_range',
        'prerequisite_shipping_price_range',
        'entitled_product_ids',
        'entitled_variant_ids',
        'entitled_collection_ids',
        'prerequisite_customer_ids',
        'once_per_customer',
        'usage_limit',
        'usage_count',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'prerequisite_subtotal_range' => 'array',
        'prerequisite_quantity_range' => 'array',
        'prerequisite_shipping_price_range' => 'array',
        'entitled_product_ids' => 'array',
        'entitled_variant_ids' => 'array',
        'entitled_collection_ids' => 'array',
        'prerequisite_customer_ids' => 'array',
        'once_per_customer' => 'boolean',
        'usage_limit' => 'integer',
        'usage_count' => 'integer',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function discountCodes()
    {
        return $this->hasMany(DiscountCode::class);
    }

    public function scopeActive($query)
    {
        return $query
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            });
    }
}
