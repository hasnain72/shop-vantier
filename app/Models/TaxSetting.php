<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaxSetting extends Model
{
    protected $fillable = [
        'taxes_included',
        'charge_taxes_on_shipping',
        'automatic_taxes',
    ];

    protected $casts = [
        'taxes_included' => 'boolean',
        'charge_taxes_on_shipping' => 'boolean',
        'automatic_taxes' => 'boolean',
    ];
}
