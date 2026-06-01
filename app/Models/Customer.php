<?php

namespace App\Models;

use App\Mail\CustomerPasswordResetMail;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Mail;
use Laravel\Sanctum\HasApiTokens;

class Customer extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'accepts_marketing',
        'marketing_opt_in_level',
        'tax_exempt',
        'tax_exemptions',
        'note',
        'tags',
        'currency',
        'locale',
        'total_spent',
        'orders_count',
        'state',
        'verified_email',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'accepts_marketing' => 'boolean',
            'verified_email' => 'boolean',
            'tax_exempt' => 'boolean',
            'tags' => 'array',
            'tax_exemptions' => 'array',
            'total_spent' => 'decimal:2',
            'password' => 'hashed',
        ];
    }

    public function addresses()
    {
        return $this->hasMany(CustomerAddress::class);
    }

    public function defaultAddress()
    {
        return $this->hasOne(CustomerAddress::class)->where('is_default', true);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function scopeEnabled($query)
    {
        return $query->where('state', 'enabled');
    }

    public function sendPasswordResetNotification($token): void
    {
        $frontendUrl = rtrim(config('app.frontend_url', env('FRONTEND_URL', 'http://localhost:4200')), '/');
        $resetUrl = $frontendUrl . '/account/reset-password?token=' . urlencode($token) . '&email=' . urlencode($this->email);
        Mail::to($this->email)->send(new CustomerPasswordResetMail($this, $resetUrl));
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }
}

