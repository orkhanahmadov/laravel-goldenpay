<?php

namespace Orkhanahmadov\LaravelGoldenpay\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Orkhanahmadov\LaravelGoldenpay\Goldenpay
 */
class Goldenpay extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'goldenpay';
    }
}
