<?php

return [
    'gateways' => ['stripe', 'paypal', 'cod', 'bank_transfer'],

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
