<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'currency',
        'currency_symbol',
        'timezone',
        'weight_unit',
        'country_code',
        'address',
        'city',
        'province',
        'zip',
        'logo',
        'favicon',
        'meta_title',
        'meta_description',
        'google_analytics_id',
        'facebook_pixel_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
