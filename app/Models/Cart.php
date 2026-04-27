<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = [
        'customer_id',
        'session_id',
        'token',
        'note',
        'attributes',
        'currency',
        'requires_shipping',
    ];

    protected $casts = [
        'attributes' => 'array',
        'requires_shipping' => 'boolean',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }
}
