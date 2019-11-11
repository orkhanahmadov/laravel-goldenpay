<?php

namespace Orkhanahmadov\LaravelGoldenpay;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Orkhanahmadov\Goldenpay\Goldenpay;
use Orkhanahmadov\LaravelGoldenpay\Http\Controllers\PaymentResultController;

class LaravelGoldenpayServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
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

            if (! class_exists('CreateGoldenpayPaymentDetailsTable')) {
                $this->publishes([
                    __DIR__.'/../database/migrations/goldenpay_payment_details_table.php.stub' =>
                        database_path('migrations/'.date('Y_m_d_His').'_create_goldenpay_payment_details_table.php'),
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
            return new Goldenpay(
                Config::get('goldenpay.auth_key'),
                Config::get('goldenpay.merchant_name')
            );
        });
    }

    private function registerRoutes()
    {
        Route::get(Config::get('goldenpay.routes.success'), [PaymentResultController::class, 'success']);
    }
}
