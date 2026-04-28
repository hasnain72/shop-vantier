<?php

namespace Database\Seeders;

use App\Models\StoreSetting;
use Illuminate\Database\Seeder;

class StoreSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'store_name',        'value' => 'Watch Accessories Store'],
            ['key' => 'store_email',        'value' => 'hello@watchstore.com'],
            ['key' => 'store_phone',        'value' => '+92-300-1234567'],
            ['key' => 'store_address',      'value' => 'Plot 12, Block A, SITE, Karachi, Pakistan'],
            ['key' => 'currency',           'value' => 'USD'],
            ['key' => 'timezone',           'value' => 'Asia/Karachi'],
            ['key' => 'weight_unit',        'value' => 'kg'],
            ['key' => 'order_prefix',       'value' => 'WAS'],
            ['key' => 'tax_rate',           'value' => '10'],
            ['key' => 'low_stock_threshold','value' => '5'],

            // Payment gateways
            ['key' => 'gateway_stripe_enabled',       'value' => '0'],
            ['key' => 'gateway_stripe_key',           'value' => ''],
            ['key' => 'gateway_stripe_secret',        'value' => ''],
            ['key' => 'gateway_paypal_enabled',       'value' => '0'],
            ['key' => 'gateway_paypal_client_id',     'value' => ''],
            ['key' => 'gateway_paypal_secret',        'value' => ''],
            ['key' => 'gateway_cod_enabled',          'value' => '1'],
            ['key' => 'gateway_bank_transfer_enabled','value' => '1'],
            ['key' => 'gateway_bank_transfer_details','value' => "Bank: HBL\nAccount: 12345678901234\nTitle: Watch Accessories Store"],
        ];

        foreach ($settings as $setting) {
            StoreSetting::updateOrCreate(['key' => $setting['key']], ['value' => $setting['value']]);
        }
    }
}
