<?php

return [

    /*
    |--------------------------------------------------------------------------
    | JWT Authentication Secret
    |--------------------------------------------------------------------------
    */
    'secret' => env('JWT_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | JWT Keys (for RSA/ECDSA)
    |--------------------------------------------------------------------------
    | Nepoužívaš – funguje len pri asymetrických algoritmoch (RS/ES).
    */
    'keys' => [
        'public' => env('JWT_PUBLIC_KEY', null),
        'private' => env('JWT_PRIVATE_KEY', null),
        'passphrase' => env('JWT_PASSPHRASE', null),
    ],

    /*
    |--------------------------------------------------------------------------
    | JWT Time To Live
    |--------------------------------------------------------------------------
    | TTL = počet minút, ako dlho je token platný (1440 = 24h)
    */
    'ttl' => env('JWT_TTL', 1440),

    /*
    |--------------------------------------------------------------------------
    | Refresh Time To Live
    |--------------------------------------------------------------------------
    | Počet minút, počas ktorých možno token obnoviť (10080 = 7 dní)
    */
    'refresh_ttl' => env('JWT_REFRESH_TTL', 10080),

    /*
    |--------------------------------------------------------------------------
    | JWT Hashing Algorithm
    |--------------------------------------------------------------------------
    | Štandard: HS256 (symetrický algoritmus)
    */
    'algo' => env('JWT_ALGO', 'HS256'),

    /*
    |--------------------------------------------------------------------------
    | Required Claims
    |--------------------------------------------------------------------------
    | Musia byť v každom JWT – neskracuj.
    */
    'required_claims' => [
        'iss', 'iat', 'exp', 'nbf', 'sub', 'jti'
    ],

    /*
    |--------------------------------------------------------------------------
    | Persistent Claims
    |--------------------------------------------------------------------------
    | Claims, ktoré zostávajú zachované po refreshi tokenu.
    */
    'persistent_claims' => [],

    /*
    |--------------------------------------------------------------------------
    | Lock Subject
    |--------------------------------------------------------------------------
    | Zabraňuje kolíziám ID používateľov pri viacerých modeloch.
    */
    'lock_subject' => true,

    /*
    |--------------------------------------------------------------------------
    | Leeway
    |--------------------------------------------------------------------------
    | Povolená odchýlka v sekundách pre exp/nbf/iat (napr. pri rozdiele času).
    */
    'leeway' => env('JWT_LEEWAY', 0),

    /*
    |--------------------------------------------------------------------------
    | Blacklist Enabled
    |--------------------------------------------------------------------------
    | Ak má byť možné invalidovať (odhlásiť) tokeny.
    */
    'blacklist_enabled' => env('JWT_BLACKLIST_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Blacklist Grace Period
    |--------------------------------------------------------------------------
    | Počet sekúnd, počas ktorých starý token ešte platí po refreshi.
    */
    'blacklist_grace_period' => env('JWT_BLACKLIST_GRACE_PERIOD', 0),

    /*
    |--------------------------------------------------------------------------
    | Show Blacklist Exception
    |--------------------------------------------------------------------------
    | Zobraziť chybu, ak je token z blacklistu (napr. po logout-e).
    */
    'show_black_list_exception' => env('JWT_SHOW_BLACKLIST_EXCEPTION', true),

    /*
    |--------------------------------------------------------------------------
    | Decrypt Cookies (nepoužíva sa v API)
    |--------------------------------------------------------------------------
    */
    'decrypt_cookies' => false,

    /*
    |--------------------------------------------------------------------------
    | Providers
    |--------------------------------------------------------------------------
    | Implementácie používané pre tokeny, autentifikáciu a blacklist.
    */
    'providers' => [
        'user' => PHPOpenSourceSaver\JWTAuth\Providers\User\EloquentUserAdapter::class,
        'jwt' => PHPOpenSourceSaver\JWTAuth\Providers\JWT\Lcobucci::class,
        'auth' => PHPOpenSourceSaver\JWTAuth\Providers\Auth\Illuminate::class,
        'storage' => PHPOpenSourceSaver\JWTAuth\Providers\Storage\Illuminate::class,
    ],
];
