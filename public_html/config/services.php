<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain'   => env('MAILGUN_DOMAIN'),
        'secret'   => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme'   => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key'    => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
    'github' => [
        'client_id'     => 'Ov23livue0mk8C3fnzhB',
        'client_secret' => '9eea8bd7d5ed01248dc6ef1aa9676ed1bf17743c',
        'redirect'      => '/github/callback',
    ],
    'google' => [
        'client_id'     => '1017638395857-u0sgogd8d2ni0atg5us9vmlhna7ugvnc.apps.googleusercontent.com',
        'client_secret' => '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',
        'redirect'      => '/google/callback',
    ],
    'facebook' => [
        'client_id'     => env('FACEBOOK_CLIENT_ID'),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
        'redirect'      => '/facebook/callback',
    ],
    'apple' => [
        'client_id' => env('APPLE_BUNDLE_ID'),
    ],
    'recaptcha' => [
        'key'    => env('RECAPTCHA_SITE_KEY'),
        'secret' => env('RECAPTCHA_SECRET_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Gateways Services
    |--------------------------------------------------------------------------
    */

    'stripe' => [
        'class' => App\Services\PaymentGateways\StripeService::class,
    ],

    'paypal' => [
        'class' => App\Services\PaymentGateways\PayPalService::class,
    ],

    'paystack' => [
        'class' => App\Services\PaymentGateways\PaystackService::class,
    ],

    'yokassa' => [
        'class' => App\Services\PaymentGateways\YokassaService::class,
    ],

    'iyzico' => [
        'class' => App\Services\PaymentGateways\IyzicoService::class,
    ],

    'razorpay' => [
        'class' => App\Services\PaymentGateways\RazorpayService::class,
    ],

    'banktransfer' => [
        'class' => App\Services\PaymentGateways\TransferService::class,
    ],

    'freeservice' => [
        'class' => App\Services\PaymentGateways\FreeService::class,
    ],

    'revenuecat' => [
        'class' => App\Services\PaymentGateways\RevenueCatService::class,
    ],

    'coinbase' => [
        'class' => App\Services\PaymentGateways\CoinbaseService::class,
    ],

    'coingate' => [
        'class' => App\Services\PaymentGateways\CoingateService::class,
    ],

    'paddle' => [
        'class' => App\Services\PaymentGateways\PaddleService::class,
    ],

    'cryptomus' => [
        'class' => App\Services\PaymentGateways\CryptomusService::class,
    ],

    'midtrans' => [
        'class' => App\Services\PaymentGateways\MidtransService::class,
    ],
];
