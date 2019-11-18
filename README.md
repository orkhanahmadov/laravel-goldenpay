# WIP

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

## Usage

First, set your Goldenpay merchant name and auth key in `.env` file.
You can get these from [Goldenpay Dashboard](https://rest.goldenpay.az/merchant/).

``` shell script
GOLDENPAY_AUTH_KEY=your-auth-key
GOLDENPAY_MERCHANT_NAME=your-merchant-name
```

To use the package you need instance of `Orkhanahmadov\LaravelGoldenpay\Goldenpay`.
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

### `result()`

Checks payment result based on previous payment key. Accepts 1 argument:

* `Payment` - This is Goldenpay's payment key as a string, or instance of previously created `Orkhanahmadov\LaravelGoldenpay\Models\Payment` model.

``` php
$goldenpay = app(Goldenpay::class);
$paymentModel = $goldenpay->payment(1000, CardType::MASTERCARD(), 'my payment');

$result = $goldenpay->result($result);
// or
$result = $goldenpay->result('1234-ABCD-5678');
```

Method returns updated instance of `Orkhanahmadov\LaravelGoldenpay\Models\Payment` model with Goldenpay's response.

## Controller

Goldenpay requires to have endpoints for successful and unsuccessful payment.
For each of this endpoints Goldenpay sends GET request with `payment_key` query string attached.
To get payment result you need to create a route to accept these requests and fetch result of the payment using received `payment_key`.

Package ships with a controller that helps to simplify this procedure.
To get started create a GET route for successful or unsuccessful payments,
add full URL in [Goldenpay Dashboard](https://rest.goldenpay.az/merchant/) to corresponding field.

Create a controller class for your route and extend `Orkhanahmadov\LaravelGoldenpay\Http\Controllers\GoldenpayController`.

Route:
``` php
Route::get('payments/successful', 'App\Http\Controllers\Payment\SuccessfulPaymentController@index');
```

Controller:
``` php
use Orkhanahmadov\LaravelGoldenpay\Http\Controllers\GoldenpayController;

class SuccessfulPaymentController extends GoldenpayController
{
    public function index()
    {
        // return a view or JSON, up to you
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

Besides usual Eloquent functionality this model also has specific accessors and scopes which you can utilize.

### Accessors

* `successful` - Returns true if payment marked as successful, false otherwise
* `payment_url` - Returns payment page url. Returns null if payment marked as successful
* `formatted_amount` - Returns "amount" in decimal form
* `card_number_decrypted` - Returns decrypted "card_number" value. Returns null if card number encrypting is turned off

### Scopes

* `successful()` - Filters "successful" payments only
* `pending()` - Filters "pending" payments only. Pending payments are the payments that not successful 
and either created within 30 minutes or have less than 3 payment checks.

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

// todo

## Configuration

// todo

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

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
