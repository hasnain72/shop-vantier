<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscountCode extends Model
{
    use HasFactory;
    protected $fillable = [
        'price_rule_id',
        'code',
        'usage_count',
    ];

    protected $casts = [
        'usage_count' => 'integer',
    ];

    public function priceRule()
    {
        return $this->belongsTo(PriceRule::class);
    }
}
