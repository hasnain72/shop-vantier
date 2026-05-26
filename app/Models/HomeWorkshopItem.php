<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeWorkshopItem extends Model
{
    protected $fillable = [
        'title_en', 'title_ar',
        'video_mp4', 'video_poster', 'thumb_url',
        'current_price', 'original_price', 'currency',
        'product_path',
        'modal_gallery_urls',
        'modal_body_primary_html',
        'modal_body_secondary_html',
        'size_options',
        'sort_order', 'is_active',
    ];

    protected $casts = [
        'modal_gallery_urls'        => 'array',
        'modal_body_primary_html'   => 'array',
        'modal_body_secondary_html' => 'array',
        'size_options'              => 'array',
        'is_active'                 => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
