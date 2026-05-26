<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeCustomerReview extends Model
{
    protected $fillable = [
        'author_name',
        'review_en', 'review_ar',
        'product_image_url',
        'product_name_en', 'product_name_ar',
        'current_price', 'compare_at_price',
        'currency_en', 'currency_ar',
        'sort_order', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
