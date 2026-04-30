<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\StoreSetting;

class ShopController extends Controller
{
    public function show()
    {
        $keys = ['store_name','store_email','currency','timezone','weight_unit','store_logo','store_favicon','meta_title','meta_description'];
        $s    = StoreSetting::whereIn('key', $keys)->pluck('value', 'key');

        $currencySymbols = ['USD' => '$','EUR' => '€','GBP' => '£','PKR' => '₨','AED' => 'د.إ','CAD' => 'CA$','AUD' => 'A$'];
        $currency        = $s['currency'] ?? 'USD';

        return response()->json([
            'success' => true,
            'data'    => ['shop' => [
                'name'             => $s['store_name']        ?? config('app.name'),
                'email'            => $s['store_email']       ?? null,
                'currency'         => $currency,
                'currency_symbol'  => $currencySymbols[$currency] ?? '$',
                'timezone'         => $s['timezone']          ?? 'UTC',
                'weight_unit'      => $s['weight_unit']       ?? 'kg',
                'logo'             => ($s['store_logo']    ?? null) ? asset('storage/'.($s['store_logo']    ?? '')) : null,
                'favicon'          => ($s['store_favicon'] ?? null) ? asset('storage/'.($s['store_favicon'] ?? '')) : null,
                'meta_title'       => $s['meta_title']        ?? null,
                'meta_description' => $s['meta_description']  ?? null,
            ]],
        ]);
    }
}
