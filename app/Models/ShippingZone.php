<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingZone extends Model
{
    protected $fillable = [
        'name',
        'countries',
        'provinces',
    ];

    protected $casts = [
        'countries' => 'array',
        'provinces' => 'array',
    ];

    public function rates()
    {
        return $this->hasMany(ShippingRate::class);
    }
}
