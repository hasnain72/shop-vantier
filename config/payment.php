<?php

return [
    'gateways' => ['stripe', 'paypal', 'myfatoorah', 'cod', 'bank_transfer'],

    'myfatoorah' => [
        // Sandbox default. For production, override MYFATOORAH_BASE_URL and MYFATOORAH_ENV in .env.
        'env'            => env('MYFATOORAH_ENV', 'sandbox'), // 'sandbox' | 'live'
        'base_url'       => env('MYFATOORAH_BASE_URL', 'https://apitest.myfatoorah.com'),
        'api_key'        => env('MYFATOORAH_API_KEY'),
        'webhook_secret' => env('MYFATOORAH_WEBHOOK_SECRET'),
        // Storefront app base (used to build return URLs the browser will land on).
        'return_url'     => env('MYFATOORAH_RETURN_URL', 'http://localhost:4200/checkout/success'),
        'error_url'      => env('MYFATOORAH_ERROR_URL',  'http://localhost:4200/checkout/failed'),
        // MF supports KWD, SAR, AED, USD, EUR, GBP, BHD, QAR, OMR, JOD.
        'default_currency' => env('MYFATOORAH_CURRENCY', 'KWD'),
    ],


    'stripe' => [
        'key'            => env('STRIPE_KEY'),
        'secret'         => env('STRIPE_SECRET'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
    ],

    'paypal' => [
        'client_id'     => env('PAYPAL_CLIENT_ID'),
        'client_secret' => env('PAYPAL_CLIENT_SECRET'),
        'mode'          => env('PAYPAL_MODE', 'sandbox'),
    ],

    'cod' => [
        'enabled' => env('COD_ENABLED', true),
        'fee'     => env('COD_FEE', 0),
    ],

    'bank_transfer' => [
        'enabled'         => env('BANK_TRANSFER_ENABLED', true),
        'account_details' => env('BANK_TRANSFER_DETAILS', ''),
    ],
];
