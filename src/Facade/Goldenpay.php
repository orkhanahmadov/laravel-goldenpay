<?php

namespace Orkhanahmadov\LaravelGoldenpay\Facade;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Orkhanahmadov\LaravelGoldenpay\LaravelGoldenpay
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
