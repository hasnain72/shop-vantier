<?php

namespace Database\Seeders;

use App\Models\TaxRate;
use App\Models\TaxSetting;
use Illuminate\Database\Seeder;

class TaxSeeder extends Seeder
{
    public function run(): void
    {
        // ── Global tax settings ───────────────────────────────────────────────────
        TaxSetting::firstOrCreate(
            ['id' => 1],
            [
                'taxes_included'          => false,
                'charge_taxes_on_shipping'=> false,
                'automatic_taxes'         => false,
            ]
        );

        // ── Country / province tax rates ─────────────────────────────────────────
        $rates = [
            // Pakistan — Federal GST 17%
            ['PK', null,   'Federal GST',          0.1700],
            ['PK', 'SD',   'Sindh Sales Tax',       0.1300],
            ['PK', 'PB',   'Punjab Sales Tax',      0.1600],

            // United States — state sales tax (no federal)
            ['US', 'CA',   'California Sales Tax',  0.0725],
            ['US', 'NY',   'New York Sales Tax',     0.0400],
            ['US', 'TX',   'Texas Sales Tax',        0.0625],
            ['US', 'FL',   'Florida Sales Tax',      0.0600],
            ['US', 'WA',   'Washington Sales Tax',   0.0650],

            // United Kingdom — VAT 20%
            ['GB', null,   'VAT',                   0.2000],

            // UAE — VAT 5%
            ['AE', null,   'VAT',                   0.0500],

            // Canada
            ['CA', 'ON',   'Ontario HST',           0.1300],
            ['CA', 'BC',   'British Columbia GST',  0.0500],
            ['CA', 'QC',   'Quebec QST',            0.0998],

            // European Union countries — VAT
            ['DE', null,   'MwSt (VAT)',             0.1900],
            ['FR', null,   'TVA (VAT)',              0.2000],
            ['NL', null,   'BTW (VAT)',              0.2100],
            ['IT', null,   'IVA (VAT)',              0.2200],
            ['ES', null,   'IVA (VAT)',              0.2100],

            // Australia — GST 10%
            ['AU', null,   'GST',                   0.1000],

            // Saudi Arabia — VAT 15%
            ['SA', null,   'VAT',                   0.1500],
        ];

        foreach ($rates as [$country, $province, $name, $rate]) {
            TaxRate::firstOrCreate(
                [
                    'country_code'  => $country,
                    'province_code' => $province,
                    'name'          => $name,
                ],
                ['rate' => $rate]
            );
        }
    }
}
