<?php

namespace Orkhanahmadov\LaravelGoldenpay\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Orkhanahmadov\Goldenpay\PaymentInterface;
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

    /**
     * Set up the database.
     */
    protected function setUpDatabase()
    {
        include_once __DIR__ . '/../database/migrations/goldenpay_payments_table.php.stub';
        (new \CreateGoldenpayPaymentsTable())->up();
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpDatabase();

//        $this->app->bind(PaymentInterface::class, FakePaymentLibrary::class);
    }
}
