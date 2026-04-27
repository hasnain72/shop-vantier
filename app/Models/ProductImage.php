<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    protected $fillable = [
        'product_id',
        'variant_ids',
        'src',
        'alt',
        'position',
        'width',
        'height',
    ];

    protected $casts = [
        'variant_ids' => 'array',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
