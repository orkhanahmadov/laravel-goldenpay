<?php

namespace Orkhanahmadov\LaravelGoldenpay\Facades;

use Illuminate\Support\Facades\Facade;
use Orkhanahmadov\Goldenpay\Enums\CardType;
use Orkhanahmadov\Goldenpay\Enums\Language;
use Orkhanahmadov\LaravelGoldenpay\Models\Payment;

/**
 * @method static Payment payment(int $amount, CardType $cardType, string $description, ?Language $lang = null)
 * @method static Payment result($payment)
 *
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
