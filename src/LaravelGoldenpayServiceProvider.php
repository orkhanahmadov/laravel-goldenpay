<?php

namespace Orkhanahmadov\LaravelGoldenpay;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Orkhanahmadov\Goldenpay\PaymentInterface;
use Orkhanahmadov\LaravelGoldenpay\Http\Controllers\FailedPaymentController;
use Orkhanahmadov\LaravelGoldenpay\Http\Controllers\SuccessfulPaymentController;
use Orkhanahmadov\Goldenpay\Goldenpay as Library;

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

            // Publishing the views.
            /*$this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/laravel-goldenpay'),
            ], 'views');*/

            // Publishing assets.
            /*$this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/laravel-goldenpay'),
            ], 'assets');*/

            // Publishing the translation files.
            /*$this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/laravel-goldenpay'),
            ], 'lang');*/

            // Registering package commands.
            // $this->commands([]);
        }

        $this->registerRoutes();
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
        Route::get(Config::get('goldenpay.routes.success'), SuccessfulPaymentController::class);
        Route::get(Config::get('goldenpay.routes.fail'), FailedPaymentController::class);
    }
}
