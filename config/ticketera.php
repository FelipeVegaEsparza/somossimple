<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Comisión de plataforma
    |--------------------------------------------------------------------------
    |
    | Preparada para futuras liquidaciones. Se aplica por evento mediante
    | commission_rate; el valor por defecto está en cero (sin comisión).
    |
    */

    'commission' => [
        'default_rate' => env('TICKETERA_COMMISSION_RATE', 0),
    ],

    /*
    |--------------------------------------------------------------------------
    | Pasarelas de pago
    |--------------------------------------------------------------------------
    |
    | Solo el modo "manual" está habilitado por defecto. Las pasarelas reales
    | quedan desacopladas y se habilitan con credenciales. Mientras no estén
    | configuradas, el sistema nunca las simula.
    |
    */

    'payments' => [
        'default' => env('TICKETERA_PAYMENT_PROVIDER', 'manual'),

        'providers' => [
            'manual' => [
                'enabled' => true,
                'label' => 'Pago manual',
            ],
            'webpay' => [
                'enabled' => env('TICKETERA_WEBPAY_ENABLED', false),
                'label' => 'Webpay',
                'commerce_code' => env('TICKETERA_WEBPAY_COMMERCE_CODE'),
                'api_key' => env('TICKETERA_WEBPAY_API_KEY'),
            ],
            'mercadopago' => [
                'enabled' => env('TICKETERA_MERCADOPAGO_ENABLED', false),
                'label' => 'Mercado Pago',
                'access_token' => env('TICKETERA_MERCADOPAGO_ACCESS_TOKEN'),
            ],
            'flow' => [
                'enabled' => env('TICKETERA_FLOW_ENABLED', false),
                'label' => 'Flow',
                'api_key' => env('TICKETERA_FLOW_API_KEY'),
                'secret_key' => env('TICKETERA_FLOW_SECRET_KEY'),
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Wallet de entradas
    |--------------------------------------------------------------------------
    |
    | Estructura preparada para agregar entradas a Apple/Google Wallet. No se
    | simula la integración mientras no exista configuración real.
    |
    */

    'wallet' => [
        'apple' => ['enabled' => env('TICKETERA_APPLE_WALLET_ENABLED', false)],
        'google' => ['enabled' => env('TICKETERA_GOOGLE_WALLET_ENABLED', false)],
    ],

];
