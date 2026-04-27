<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'commentable',
        'meta_title',
        'meta_description',
    ];

    public function articles()
    {
        return $this->hasMany(Article::class);
    }
}
