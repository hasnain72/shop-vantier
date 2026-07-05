<?php

return [
    'providers' => ['smsa'],

    'smsa' => [
        // Sandbox default. For production override in .env.
        'env'         => env('SMSA_ENV', 'sandbox'),      // 'sandbox' | 'live'
        'base_url'    => env('SMSA_BASE_URL', 'https://track.smsaexpress.com/api'),
        'passkey'     => env('SMSA_PASSKEY'),
        'account_no'  => env('SMSA_ACCOUNT_NO'),
        // Storefront return / label callback (optional).
        'webhook_secret' => env('SMSA_WEBHOOK_SECRET'),
        // Default sender values used if a location doesn't override them.
        'sender' => [
            'name'    => env('SMSA_SENDER_NAME', 'Vantier Store'),
            'contact' => env('SMSA_SENDER_CONTACT', 'Store Manager'),
            'phone'   => env('SMSA_SENDER_PHONE'),
            'email'   => env('SMSA_SENDER_EMAIL'),
            'city'    => env('SMSA_SENDER_CITY', 'Riyadh'),
            'country' => env('SMSA_SENDER_COUNTRY', 'SA'),
        ],
        // Default declared value & currency for the AWB when order total isn't KWD/SAR.
        'default_currency' => env('SMSA_CURRENCY', 'SAR'),
        // Service code — check with your SMSA account manager: PDO (Prepaid), CCO (COD), DLV, XPS, etc.
        'default_service' => env('SMSA_SERVICE_CODE', 'PDO'),
    ],
];
