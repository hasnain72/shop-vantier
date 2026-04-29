<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Webhook extends Model
{
    protected $fillable = [
        'topic',
        'address',
        'format',
        'fields',
        'api_version',
        'is_active',
        'secret',
        'last_triggered_at',
        'last_status_code',
        'failure_count',
    ];

    protected $casts = [
        'fields'             => 'array',
        'is_active'          => 'boolean',
        'last_triggered_at'  => 'datetime',
        'failure_count'      => 'integer',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Webhook $webhook) {
            if (!$webhook->secret) {
                $webhook->secret = Str::random(32);
            }
        });
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public static function availableTopics(): array
    {
        return [
            'orders/create',
            'orders/fulfilled',
            'orders/cancelled',
            'orders/refunded',
            'customers/create',
            'inventory/low_stock',
        ];
    }
}
