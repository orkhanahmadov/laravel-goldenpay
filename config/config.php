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
    | Database table name
    |--------------------------------------------------------------------------
    |
    | Defines name for table in database.
    |
    */

    'table_name' => 'goldenpay_payments',

    /*
    |--------------------------------------------------------------------------
    | Encrypt card numbers
    |--------------------------------------------------------------------------
    |
    | Set to "false" if you don't want "card_numbers" field to be encrypted.
    |
    */

    'encrypt_card_numbers' => true,

    /*
    |--------------------------------------------------------------------------
    | Payment events
    |--------------------------------------------------------------------------
    |
    | Defines event types and event classes for each payment event.
    | Each event receives instance of payment related "Orkhanahmadov\LaravelGoldenpay\Models\Payment" model.
    |
    */

    'payment_events' => [

        /*
        |--------------------------------------------------------------------------
        | Enables/disables payment events
        |--------------------------------------------------------------------------
        |
        | Set to "false" if you want to disable all payment events.
        |
        */
        'enabled' => true,

        /*
        |--------------------------------------------------------------------------
        | Payment event types
        |--------------------------------------------------------------------------
        |
        | Lists all possible event types and related event classes.
        | If you want to use your own event classes for specific events, you can replace them here.
        |
        | Each event class needs to implement "Orkhanahmadov\LaravelGoldenpay\Events\PaymentEvent" class.
        |
        */

        'created' => \Orkhanahmadov\LaravelGoldenpay\Events\PaymentCreatedEvent::class,
        'checked' => \Orkhanahmadov\LaravelGoldenpay\Events\PaymentCheckedEvent::class,
        'successful' => \Orkhanahmadov\LaravelGoldenpay\Events\PaymentSuccessfulEvent::class,

    ],

];
