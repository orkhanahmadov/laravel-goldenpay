<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Goldenpay auth key
    |--------------------------------------------------------------------------
    |
    | Auth key for Goldenpay. You can get it from: https://rest.goldenpay.az/merchant/
    |
    */

    'auth_key' => env('GOLDENPAY_AUTH_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Goldenpay merchant name
    |--------------------------------------------------------------------------
    |
    | Merchant name for Goldenpay. You can get it from: https://rest.goldenpay.az/merchant/
    |
    */

    'merchant_name' => env('GOLDENPAY_MERCHANT_NAME'),

    /*
    |--------------------------------------------------------------------------
    | Goldenpay routes
    |--------------------------------------------------------------------------
    |
    | These routes are required by Goldenpay and first needs to be registered in Goldenpay Dashboard:
    | https://rest.goldenpay.az/merchant/
    |
    | First go to dashboard, and specify your desired success and fail endpoints,
    | then customize this config array with your endpoints.
    |
    | Example:
    | If in Goldenpay dashboard you specified your success endpoint as: https://your-domain.com/goldenpay/success
    | then in this file set success route needs to be "goldenpay/success". Same for fail route.
    |
    */

    'routes' => [
        'success' => 'goldenpay/success',
        'fail' => 'goldenpay/fail',
    ],

    /*
    |--------------------------------------------------------------------------
    | Database table name
    |--------------------------------------------------------------------------
    |
    | Defines name for table in database.
    |
    */

    'table_name' => 'goldenpay_payments',

    /*
    |--------------------------------------------------------------------------
    | Events
    |--------------------------------------------------------------------------
    |
    | Defines event classes for payment events.
    | Each event receives instance of related \Orkhanahmadov\LaravelGoldenpay\Models\Payment model.
    |
    */

    // todo: rename to "payment_events"
    'payment_events' => [
        'enabled' => true,

        'created' => \Orkhanahmadov\LaravelGoldenpay\Events\PaymentCreatedEvent::class,
        'checked' => \Orkhanahmadov\LaravelGoldenpay\Events\PaymentCheckedEvent::class,
        'successful' => \Orkhanahmadov\LaravelGoldenpay\Events\PaymentSuccessfulEvent::class,
    ],

];
