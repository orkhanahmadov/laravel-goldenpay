<?php

namespace Orkhanahmadov\LaravelGoldenpay\Tests;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Orchestra\Testbench\TestCase as Orchestra;
use Orkhanahmadov\Goldenpay\PaymentInterface;
use Orkhanahmadov\LaravelGoldenpay\Facades\Goldenpay;
use Orkhanahmadov\LaravelGoldenpay\LaravelGoldenpayServiceProvider;

class TestCase extends Orchestra
{
    /**
     * @var \Orkhanahmadov\LaravelGoldenpay\Goldenpay
     */
    protected $goldenpay;

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
        include_once __DIR__.'/../database/migrations/goldenpay_payments_table.php.stub';
        (new \CreateGoldenpayPaymentsTable())->up();

        DB::statement('CREATE TABLE fake_payable_models (id INTEGER PRIMARY KEY AUTOINCREMENT, name VARCHAR);');
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpDatabase();
        $this->withFactories(__DIR__.'/../database/factories');

        $this->app->bind(PaymentInterface::class, FakePaymentLibrary::class);
        $this->goldenpay = $this->app->make(\Orkhanahmadov\LaravelGoldenpay\Goldenpay::class);

        Event::fake();
    }
}
