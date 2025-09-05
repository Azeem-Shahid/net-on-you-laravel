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
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'coinpayments' => [
        'enabled' => env('COINPAYMENTS_ENABLED', false),
        'merchant_id' => env('COINPAYMENTS_MERCHANT_ID'),
        'public_key' => env('COINPAYMENTS_PUBLIC_KEY'),
        'private_key' => env('COINPAYMENTS_PRIVATE_KEY'),
        'ipn_secret' => env('COINPAYMENTS_IPN_SECRET'),
        'ipn_url' => env('COINPAYMENTS_IPN_URL'),
        'sandbox' => env('COINPAYMENTS_SANDBOX', false),
        'currency2' => env('COINPAYMENTS_CURRENCY2', 'USDT.TRC20'),
        'subscription_price' => env('SUBSCRIPTION_PRICE', '39.90'),
    ],

];
