<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Integraciones de Wallet
    |--------------------------------------------------------------------------
    |
    | La generación real de tarjetas para Apple Wallet y Google Wallet queda
    | desacoplada y deshabilitada por defecto. Mientras no existan credenciales
    | y configuración, la interfaz lo mostrará como "pendiente de configuración"
    | y nunca simulará una integración inexistente.
    |
    */

    'wallet' => [

        'apple' => [
            'enabled' => env('LOYALTY_APPLE_WALLET_ENABLED', false),
            'pass_type_identifier' => env('LOYALTY_APPLE_PASS_TYPE_ID'),
            'team_identifier' => env('LOYALTY_APPLE_TEAM_ID'),
            'certificate_path' => env('LOYALTY_APPLE_CERTIFICATE_PATH'),
            'certificate_password' => env('LOYALTY_APPLE_CERTIFICATE_PASSWORD'),
            'web_service_url' => env('LOYALTY_APPLE_WEB_SERVICE_URL'),
        ],

        'google' => [
            'enabled' => env('LOYALTY_GOOGLE_WALLET_ENABLED', false),
            'issuer_id' => env('LOYALTY_GOOGLE_ISSUER_ID'),
            'service_account_path' => env('LOYALTY_GOOGLE_SERVICE_ACCOUNT_PATH'),
            'class_suffix' => env('LOYALTY_GOOGLE_CLASS_SUFFIX', 'somossimple_loyalty'),
        ],

    ],

];
