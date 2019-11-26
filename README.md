# :credit_card: [GoldenPay](http://www.goldenpay.az) package for Laravel

[![Latest Stable Version](https://poser.pugx.org/orkhanahmadov/laravel-goldenpay/v/stable)](https://packagist.org/packages/orkhanahmadov/laravel-goldenpay)
[![Latest Unstable Version](https://poser.pugx.org/orkhanahmadov/laravel-goldenpay/v/unstable)](https://packagist.org/packages/orkhanahmadov/laravel-goldenpay)
[![Total Downloads](https://img.shields.io/packagist/dt/orkhanahmadov/laravel-goldenpay)](https://packagist.org/packages/orkhanahmadov/laravel-goldenpay)
[![GitHub license](https://img.shields.io/github/license/orkhanahmadov/laravel-goldenpay.svg)](https://github.com/orkhanahmadov/laravel-goldenpay/blob/master/LICENSE.md)

[![Build Status](https://img.shields.io/travis/orkhanahmadov/laravel-goldenpay.svg)](https://travis-ci.org/orkhanahmadov/laravel-goldenpay)
[![Test Coverage](https://api.codeclimate.com/v1/badges/103bdc08629fc844b570/test_coverage)](https://codeclimate.com/github/orkhanahmadov/laravel-goldenpay/test_coverage)
[![Maintainability](https://api.codeclimate.com/v1/badges/103bdc08629fc844b570/maintainability)](https://codeclimate.com/github/orkhanahmadov/laravel-goldenpay/maintainability)
[![Quality Score](https://img.shields.io/scrutinizer/g/orkhanahmadov/laravel-goldenpay.svg)](https://scrutinizer-ci.com/g/orkhanahmadov/laravel-goldenpay)
[![StyleCI](https://github.styleci.io/repos/220855997/shield?branch=master)](https://github.styleci.io/repos/220855997)

Full-feature Laravel package for Goldenpay integration.

# Table of Contents

1. [Requirements](#requirements)
2. [Installation](#installation)
3. [Usage](#usage)
3. [Controller](#controller)
4. [Models](#models)
4. [Commands](#commands)
5. [Events](#events)
6. [Configuration](#configuration)

## Requirements

- PHP **^7.2**
- Laravel **5.8.*** or **^6.0**

## Installation

You can install the package via composer:

```bash
composer require orkhanahmadov/laravel-goldenpay
```

Run this command to publish required migration file:
``` shell script
php artisan vendor:publish --provider="Orkhanahmadov\LaravelGoldenpay\LaravelGoldenpayServiceProvider" --tag=migrations
```

## Usage

First, set your Goldenpay merchant name and auth key in `.env` file.
You can get these from [Goldenpay Dashboard](https://rest.goldenpay.az/merchant/).

``` shell script
GOLDENPAY_AUTH_KEY=your-auth-key
GOLDENPAY_MERCHANT_NAME=your-merchant-name
```

To use Goldenpay service you need instance of `Orkhanahmadov\LaravelGoldenpay\Goldenpay`.
You can instantiate this class using Laravel's service container, for example by injecting to your controller

``` php
use Orkhanahmadov\LaravelGoldenpay\Goldenpay;

class MyController
{
    public function index(Goldenpay $goldenpay)
    {
        //
    }
}
```

Or you can use Laravel's service resolver to create instance of the class:

``` php
use Orkhanahmadov\LaravelGoldenpay\Goldenpay;

class MyClass
{
    public function doSomething()
    {
        $goldenpay = app(Goldenpay::class);
        //
    }
}
```

### Available methods:

### `payment()`

Prepares payment based on passed credentials and accepts 4 arguments:

* `Amount` - Payment amount, only integer values accepted
For example, for you want to create payment for 10.25, then pass it as 1025.
* `Card type` - Requires instance of `Orkhanahmadov\Goldenpay\Enums\CardType`.
`CardType::VISA()` for VISA, `CardType::MASTERCARD()` for MasterCard
* `Description` - Payment description
* `Language` *(optional)* - Sets payment page interface language. Requires instance of `Orkhanahmadov\Goldenpay\Enums\Language`.
`Language::EN()` for english, `Language::RU()` for russian, `Language::AZ()` for azerbaijani.
If nothing passed service will use Laravel's active locale.

``` php
$goldenpay = app(Goldenpay::class);
$goldenpay->payment(1000, CardType::MASTERCARD(), 'my payment');
```

Method returns created instance of `Orkhanahmadov\LaravelGoldenpay\Models\Payment` model.

You can use `$payment_url` property to get unique payment URL and redirect user to this URL to start payment.

``` php
$payment = $goldenpay->payment(1000, CardType::MASTERCARD(), 'my payment');

$payment->payment_url; // redirect user to this URL to start payment
```

### `result()`

Checks payment result based on previous payment key. Accepts single argument:

* `Payment` - This is Goldenpay's payment key as a string, or instance of previously created `Orkhanahmadov\LaravelGoldenpay\Models\Payment` model.

``` php
$goldenpay = app(Goldenpay::class);
$paymentModel = $goldenpay->payment(1000, CardType::MASTERCARD(), 'my payment');

$result = $goldenpay->result($paymentModel);
// or
$result = $goldenpay->result('1234-ABCD-5678');
```

Method returns updated instance of `Orkhanahmadov\LaravelGoldenpay\Models\Payment` model with Goldenpay's response.

## Controller

Goldenpay requires to have endpoints for successful and unsuccessful payment.
For each of this endpoints Goldenpay sends GET request with `payment_key` query string attached.
To get payment result you need to create a route to accept these requests and fetch result of the payment using received `payment_key`.

Package ships with a controller that helps to simplify this process.
To get started, first create a GET route for successful or unsuccessful payments 
and add full URL in [Goldenpay Dashboard](https://rest.goldenpay.az/merchant/) to corresponding field.

``` php
Route::get('payments/successful', 'App\Http\Controllers\Payment\SuccessfulPaymentController@index');
```

Create a controller class for your route and extend `Orkhanahmadov\LaravelGoldenpay\Http\Controllers\GoldenpayController`.

``` php
use Orkhanahmadov\LaravelGoldenpay\Http\Controllers\GoldenpayController;

class SuccessfulPaymentController extends GoldenpayController
{
    public function index()
    {
        // return a view or JSON, totally up to you
    }
}
```

By extending `Orkhanahmadov\LaravelGoldenpay\Http\Controllers\GoldenpayController`,
your controller will automatically check for payment result based on `payment_key` query string.

In your controller you can access to `$payment` property which will have payment results from Goldenpay.

``` php
public function index()
{
    $this->payment->status; // will return payment status 
    $this->payment->successful; // will return true if payment was successful or false if unsuccessful
}
```

You can use same endpoint for both successful and unsuccessful payments and decide what you want to show user based on
`$this->payment->successful` state or you create separate endpoints and controllers and
extend `Orkhanahmadov\LaravelGoldenpay\Http\Controllers\GoldenpayController` in both controllers.

## Models

Package ships with `Orkhanahmadov\LaravelGoldenpay\Models\Payment` Eloquent model.
Model stores following information for each payment:

* `payment_key` - string, unique payment key provided by Goldenpay
* `amount` - integer, payment amount
* `card_type` - enum, "m" for MasterCard, "v" for VISA
* `language` - enum, "en" for English, "ru" for Russian, "lv" for Azerbaijani
* `description` - string, payment description
* `status` - integer, payment status code
* `message` - string, payment status message
* `reference_number` - string, payment reference number
* `card_number` - string, encrypted, card number used for payment
* `payment_date` - datetime, payment date
* `checks` - integer, payment check count

Besides usual Eloquent functionality this model also has specific accessors, scopes 
and relationship abilities which you can utilize.

### Accessors

* `successful` - Returns `true` if payment marked as successful, `false` otherwise
* `payment_url` - Returns payment page url. Returns `null` if payment marked as successful
* `formatted_amount` - Returns "amount" in decimal form
* `card_number_decrypted` - Returns decrypted "card_number" value. Returns `null` if card number encrypting is turned off

### Scopes

* `whereSuccessful()` - Filters "successful" payments only
* `wherePending()` - Filters "pending" payments only. Pending payments are the payments that not successful 
and either created within 30 minutes or have less than 3 payment checks.

### Relationship

You can make any existing Eloquent model "payable" and attach Goldenpay payments to it.
Use `Orkhanahmadov\LaravelGoldenpay\Traits\Payable` trait in your existing model to establish direct model relationship.
Trait usage requires to have `amount()` `description()` methods to be defined in your model:

* `amount()` - Must return payment amount in integer
* `description()` - Must return description for payment instance

``` php
use Illuminate\Database\Eloquent\Model;
use Orkhanahmadov\LaravelGoldenpay\Traits\Payable;

class Product extends Model
{
    use Payable;

    protected $fillable = [
        'name',
        'color',
        'size',
        'price',
    ];

    protected $casts = [
        'price' => 'float', // lets image that you store price as float, like "15.70" in "products" table
    ];

    protected function amount(): int
    {
        // this method needs to return integer value of price
        return $this->amount * 100;
    }

    protected function description(): string
    {
        // this method needs to return description for payment instance
        // try to use both unique and easy to read identifier
        return $this->id . ' - ' . $this->name . ' - ' . $this->color;
    }
}
```

Now `Product` model has direct relationship with Goldenpay payments.
By using `Payable` your model also gets access to payment related relationships and payment methods.

#### `createPayment()`

``` php
$product = Product::find(1);

$product->createPayment(CardType::VISA()); // uses product amount() and description() to create new payment instance
```

You can also override `amount` and `description()` for specific payment:

``` php
$product->createPayment(CardType::VISA(), 2599, 'my custom description');
```

Method accepts following arguments:

* `Card type` - Instance of `Orkhanahmadov\Goldenpay\Enums\CardType`
* `Amount` *(optional)* - When used overrides `amount()` method value in model
* `Description` *(optional)* - When used overrides `description()` method value in model
* `Language` *(optional)* - When skipped will use Laravel's locale. Instance of `Orkhanahmadov\Goldenpay\Enums\Language`.

Method returns create instance of `Orkhanahmadov\LaravelGoldenpay\Models\Payment` instance.

#### `payments()`

Eloquent relationship method. Return all related payments to model.

``` php
$product = Product::find(1);
$product->payments; // returns collection of related Payment models
$product->payments()->where('amount', '>=', 10000); // use it as regular Eloquent relationship
```

#### `successfulPayments()`

Eloquent relationship method. Return all related successful payments to model.

``` php
$product = Product::find(1);
$product->successfulPayments; // returns collection of related Payment models
$product->successfulPayments()->where('amount', '>=', 10000); // use it as regular Eloquent relationship
```

## Commands

Package ships with artisan command for checking payment results.

``` shell script
php artisan goldenpay:result
```

Executing above command will loop through all "pending" payments and update their results.

Command also accepts payment key as an argument to check single payment result.

``` shell script
php artisan goldenpay:result 1234-ABCD-5678
```

Goldenpay requires manual check for payments to determine their final status.
For example, user might go to payment page then close browser window without entering anything.
These kind payment cases needs to be checked manually to finalize their status.
You can use Laravel's [Task Scheduling](https://laravel.com/docs/master/scheduling) 
for running `goldenpay:result` command on Cron job.

``` php
protected function schedule(Schedule $schedule)
{
    $schedule->command('goldenpay:result')->everyFiveMinutes();
}
```

Because Goldenpay states that each payment session is active only for 15 minutes,
it is recommended to keep frequency to 5 or 10 minutes.

## Events

Package ships with Laravel events which gets fired on specific conditions.

Available event classes:

* `Orkhanahmadov\LaravelGoldenpay\Events\PaymentCreatedEvent` - gets fired when new payment is created
* `Orkhanahmadov\LaravelGoldenpay\Events\PaymentCheckedEvent` - gets fired when payment is checked for result
* `Orkhanahmadov\LaravelGoldenpay\Events\PaymentSuccessfulEvent` - gets fired when payment finalized as successful

Each event receives instance of `Orkhanahmadov\LaravelGoldenpay\Models\Payment` Eloquent model 
as public `$payment` property.

You can set up event listeners to trigger when specific payment event gets fired.

``` php
protected $listen = [
    'Orkhanahmadov\LaravelGoldenpay\Events\PaymentSuccessfulEvent' => [
        'App\Listeners\SendPaymentInvoice',
        'App\Listeners\SendProductLicense',
    ],
];
```

## Configuration

Run this command to publish package config file:

``` shell script
php artisan vendor:publish --provider="Orkhanahmadov\LaravelGoldenpay\LaravelGoldenpayServiceProvider" --tag=config
```

Config file contains following settings:

* `auth_key` - Defines Goldenpay "auth key", defaults to `.env` variable
* `merchant_name` - Defines Goldenpay "merchant name", defaults to `.env` variable
* `table_name` - Defines name for Goldenpay payments database table. Default: "goldenpay_payments"
* `encrypt_card_numbers` - Defines if "card_number" field needs to be automatically encrypted
when when creating payments or getting payment results. Default is `true`, 
change to `false` if you want to disable automatic encryption. Recommended to leave it `true` for extra layer of security.
**Warning!** If you already have records in Payments table, changing this value will break encryption/decryption.
Old values won't be encrypted/decrypted automatically, you need to do it manually.
* `payment_events` - Payment events related settings
    * `enabled` - Defines if payment events are enabled. Set to `false` to disable all payment events
    * `checked` - "Payment checked" event class. By default uses `Orkhanahmadov\LaravelGoldenpay\Events\PaymentCreatedEvent` class
    * `created` - "Payment created" event class. By default uses `Orkhanahmadov\LaravelGoldenpay\Events\PaymentCheckedEvent` class
    * `successful` - "Payment successful" event class. By default uses `Orkhanahmadov\LaravelGoldenpay\Events\PaymentSuccessfulEvent` class

If you want to use your own event class for specific payment event you can replace class namespace with your class namespace.
Each payment event receives instance of `Orkhanahmadov\LaravelGoldenpay\Models\Payment` Eloquent model. 
Because of this, make sure you add payment model as dependency to your event class constructor signature or 
you can extend `Orkhanahmadov\LaravelGoldenpay\Events\PaymentEvent` class which already has payment model as dependency.

Setting specific payment event to `null` disables that event without interrupting others.

### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email ahmadov90@gmail.com instead of using the issue tracker.

## Credits

- [Orkhan Ahmadov](https://github.com/orkhanahmadov)
- [All Contributors](../../contributors)

## Support me

If you like my work, if my open-source contributions help you or your company, 
please consider supporting me through [Github Sponsors](https://github.com/sponsors/orkhanahmadov).

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
