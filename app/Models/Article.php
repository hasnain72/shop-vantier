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
        'author_ar',
        'title',
        'title_ar',
        'slug',
        'body_html',
        'body_html_ar',
        'summary_html',
        'summary_html_ar',
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
