<?php

namespace Orkhanahmadov\LaravelGoldenpay;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Orkhanahmadov\Goldenpay\PaymentInterface;
use Orkhanahmadov\Goldenpay\Goldenpay as Library;
use Orkhanahmadov\LaravelGoldenpay\Commands\ResultCommand;
use Orkhanahmadov\LaravelGoldenpay\Tests\FakeResultController;

class LaravelGoldenpayServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->app->bind(PaymentInterface::class, Library::class);

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('goldenpay.php'),
            ], 'config');

            if (! class_exists('CreateGoldenpayPaymentsTable')) {
                $this->publishes([
                    __DIR__.'/../database/migrations/goldenpay_payments_table.php.stub' =>
                        database_path('migrations/'.date('Y_m_d_His').'_create_goldenpay_payments_table.php'),
                ], 'migrations');
            }

            $this->commands([
                ResultCommand::class,
            ]);

            $this->registerRoutes();
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'goldenpay');

        $this->app->singleton('goldenpay', function () {
            return $this->app->make(Goldenpay::class);
        });
    }

    /**
     * Registers package routes.
     */
    private function registerRoutes(): void
    {
        if ($this->app->environment('testing')) {
            Route::get('__goldenpay-fake-test-route__', [FakeResultController::class, 'index']);
        }
    }
}
