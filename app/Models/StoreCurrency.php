<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreCurrency extends Model
{
    protected $fillable = [
        'currency',
        'currency_symbol',
        'rate_from_base',
        'is_active',
        'is_primary',
    ];

    protected $casts = [
        'rate_from_base' => 'decimal:6',
        'is_active' => 'boolean',
        'is_primary' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
