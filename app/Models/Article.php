<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'blog_id',
        'author',
        'title',
        'slug',
        'body_html',
        'summary_html',
        'image',
        'tags',
        'published',
        'published_at',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'tags' => 'array',
        'published' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function blog()
    {
        return $this->belongsTo(Blog::class);
    }

    public function scopePublished($query)
    {
        return $query->where('published', true);
    }
}
