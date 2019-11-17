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

- PHP *^7.2*
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

## Controller

// todo

## Models

// todo

## Commands

// todo

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
