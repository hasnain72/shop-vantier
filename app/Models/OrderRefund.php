<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderRefund extends Model
{
    protected $fillable = [
        'order_id',
        'user_id',
        'note',
        'restock',
        'refund_line_items',
        'transactions',
    ];

    protected $casts = [
        'restock' => 'boolean',
        'refund_line_items' => 'array',
        'transactions' => 'array',
    ];

    public function getAmountAttribute(): float
    {
        return collect($this->transactions)->sum('amount');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
