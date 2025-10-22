<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Laravel CORS Configuration
    |--------------------------------------------------------------------------
    |
    | Tu definujeme, ktoré domény môžu komunikovať s tvojím API.
    | Hviezdička (*) znamená „povoliť všetky“ — vhodné pre vývoj.
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['*'], // <– TOTO povoľuje všetky požiadavky (aj z localhost)

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
