<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'customer_id',
        'order_number',
        'name',
        'email',
        'phone',
        'financial_status',
        'fulfillment_status',
        'currency',
        'subtotal_price',
        'total_discounts',
        'total_tax',
        'total_shipping',
        'total_price',
        'total_weight',
        'taxes_included',
        'discount_codes',
        'note',
        'tags',
        'note_attributes',
        'source_name',
        'source_identifier',
        'cancel_reason',
        'cancelled_at',
        'closed_at',
        'processed_at',
        'confirmed',
        'buyer_accepts_marketing',
        'ip_address',
        'user_agent',
        'referring_site',
        'landing_site',
        'cart_token',
        'checkout_token',
        'shipping_address',
        'billing_address',
        'client_details',
    ];

    protected $casts = [
        'subtotal_price' => 'decimal:2',
        'total_discounts' => 'decimal:2',
        'total_tax' => 'decimal:2',
        'total_shipping' => 'decimal:2',
        'total_price' => 'decimal:2',
        'total_weight' => 'decimal:3',
        'taxes_included' => 'boolean',
        'discount_codes' => 'array',
        'tags' => 'array',
        'note_attributes' => 'array',
        'cancelled_at' => 'datetime',
        'closed_at' => 'datetime',
        'processed_at' => 'datetime',
        'confirmed' => 'boolean',
        'buyer_accepts_marketing' => 'boolean',
        'shipping_address' => 'array',
        'billing_address' => 'array',
        'client_details' => 'array',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function lineItems()
    {
        return $this->hasMany(OrderLineItem::class);
    }

    public function fulfillments()
    {
        return $this->hasMany(OrderFulfillment::class);
    }

    public function refunds()
    {
        return $this->hasMany(OrderRefund::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
