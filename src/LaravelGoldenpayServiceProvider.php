<?php

namespace Orkhanahmadov\LaravelGoldenpay;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Orkhanahmadov\Goldenpay\Goldenpay;
use Orkhanahmadov\LaravelGoldenpay\Controllers\PaymentResultController;

class LaravelGoldenpayServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        /*
         * Optional methods to load your package assets
         */
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'laravel-goldenpay');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'laravel-goldenpay');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        Route::get(Config::get('goldenpay.routes.success'), [PaymentResultController::class, 'success']);

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('goldenpay.php'),
            ], 'config');

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
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'goldenpay');

        $this->app->singleton('goldenpay', function () {
            return new Goldenpay(
                Config::get('goldenpay.auth_key'),
                Config::get('goldenpay.merchant_name')
            );
        });
    }
}
