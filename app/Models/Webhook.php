<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        'failure_count',
    ];

    protected $casts = [
        'fields' => 'array',
        'is_active' => 'boolean',
        'last_triggered_at' => 'datetime',
        'failure_count' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
