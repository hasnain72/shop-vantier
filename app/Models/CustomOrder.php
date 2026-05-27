<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomOrder extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'message',
        'status',
        'admin_notes',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public const STATUSES = ['new', 'read', 'replied', 'closed'];

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'new'     => 'badge bg-danger',
            'read'    => 'badge bg-warning text-dark',
            'replied' => 'badge bg-info text-dark',
            'closed'  => 'badge bg-secondary',
            default   => 'badge bg-secondary',
        };
    }
}
