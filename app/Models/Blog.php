<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;
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
