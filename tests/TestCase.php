<?php

namespace Orkhanahmadov\LaravelGoldenpay\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Orkhanahmadov\LaravelGoldenpay\LaravelGoldenpayServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            LaravelGoldenpayServiceProvider::class,
        ];
    }
}
