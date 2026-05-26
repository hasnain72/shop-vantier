<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeFaq extends Model
{
    protected $fillable = [
        'question_en', 'question_ar',
        'answer_en', 'answer_ar',
        'sort_order', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
