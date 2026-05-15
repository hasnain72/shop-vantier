<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Product extends Model
{
    use HasFactory, SoftDeletes, HasSlug;

    protected $fillable = [
        'product_type_id',
        'title',
        'slug',
        'body_html',
        'vendor',
        'product_type',
        'product_category',
        'tags',
        'status',
        'published_at',
        'template_suffix',
        'meta_title',
        'meta_description',
        'options',
        'featured_image',
        'has_only_default_variant',
        'requires_shipping',
        'taxable',
        'sort_position',
    ];

    protected $casts = [
        'tags' => 'array',
        'published_at' => 'datetime',
        'options' => 'array',
        'has_only_default_variant' => 'boolean',
        'requires_shipping' => 'boolean',
        'taxable' => 'boolean',
    ];

    public function productType()
    {
        return $this->belongsTo(ProductType::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function productImages()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function collections()
    {
        return $this->belongsToMany(Collection::class, 'collection_product')
            ->withPivot(['sort_order']);
    }

    public function addons()
    {
        return $this->hasMany(ProductAddon::class)->orderBy('position');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at');
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }
}
