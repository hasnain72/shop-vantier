<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaxRate extends Model
{
    protected $fillable = [
        'country_code',
        'province_code',
        'name',
        'rate',
    ];

    protected $casts = [
        'rate' => 'decimal:4',
    ];
}
