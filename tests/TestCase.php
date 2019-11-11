<?php

namespace Orkhanahmadov\LaravelGoldenpay\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Orkhanahmadov\LaravelGoldenpay\Facade\Goldenpay;
use Orkhanahmadov\LaravelGoldenpay\LaravelGoldenpayServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            LaravelGoldenpayServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Goldenpay' => Goldenpay::class,
        ];
    }
}
