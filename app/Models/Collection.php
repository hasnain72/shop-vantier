<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Collection extends Model
{
    use HasFactory, SoftDeletes, HasSlug;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'image',
        'sort_order',
        'template_suffix',
        'published',
        'published_at',
        'sort_position',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'published' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'collection_product')
            ->withPivot(['sort_order']);
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }

    public function scopePublished($query)
    {
        return $query->where('published', true);
    }
}
