<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderFulfillment extends Model
{
    protected $fillable = [
        'order_id',
        'location_id',
        'status',
        'tracking_company',
        'tracking_number',
        'tracking_url',
        'tracking_numbers',
        'tracking_urls',
        'shipment_status',
        'notify_customer',
        'receipt',
        'line_items',
    ];

    protected $casts = [
        'tracking_numbers' => 'array',
        'tracking_urls' => 'array',
        'notify_customer' => 'boolean',
        'receipt' => 'array',
        'line_items' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function location()
    {
        return $this->belongsTo(InventoryLocation::class, 'location_id');
    }

    public function getTrackingUrl(): string
    {
        if ($this->tracking_url) {
            return $this->tracking_url;
        }

        if ($this->tracking_company && $this->tracking_number) {
            return app(\App\Services\TrackingService::class)
                ->getTrackingUrl($this->tracking_company, $this->tracking_number);
        }

        return '#';
    }
}
