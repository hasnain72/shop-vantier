<?php

return [

    /*
     * Paths that should have CORS applied.
     */
    'paths' => ['api/*'],

    'allowed_methods' => ['*'],

    /*
     * Allow Angular dev server on any port + production domain.
     */
    'allowed_origins' => [
        'http://localhost:4200',
        'http://127.0.0.1:4200',
        'https://thevantier.com',
        'https://www.thevantier.com',
        'https://www.theventir.chronosouq.com'
    ],

    'allowed_origins_patterns' => [
        '#^http://localhost(:\d+)?$#',
        '#^http://127\.0\.0\.1(:\d+)?$#',
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
