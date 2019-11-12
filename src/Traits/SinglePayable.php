<?php

namespace Orkhanahmadov\LaravelGoldenpay\Traits;

use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Orkhanahmadov\Goldenpay\Enums\CardType;
use Orkhanahmadov\LaravelGoldenpay\Goldenpay;
use Orkhanahmadov\LaravelGoldenpay\Models\Payment;

trait SinglePayable
{
    public function payment(): MorphOne
    {
        return $this->morphOne(Payment::class, 'payable');
    }

    public function createPayment(int $amount, CardType $cardType): Payment
    {
        /** @var Goldenpay $goldenpay */
        $goldenpay = Container::getInstance()->make(Goldenpay::class);

        $payment = $goldenpay->payment($amount, $cardType, $this->description());

        $payment->payable_type = self::class;
        $payment->payable_id = $this->getKey();
        $payment->save();

        return $payment;
    }

    /**
     * Define description for this model's payments.
     *
     * @return string
     */
    abstract public function description(): string;
}
