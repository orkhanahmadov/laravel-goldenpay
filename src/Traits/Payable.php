<?php

namespace Orkhanahmadov\LaravelGoldenpay\Traits;

use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Orkhanahmadov\Goldenpay\Enums\CardType;
use Orkhanahmadov\Goldenpay\Enums\Language;
use Orkhanahmadov\LaravelGoldenpay\Goldenpay;
use Orkhanahmadov\LaravelGoldenpay\Models\Payment;

/**
 * Trait Payable.
 *
 * @mixin Model
 */
trait Payable
{
    /**
     * Returns all related payments.
     *
     * @return MorphMany
     */
    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'payable');
    }

    /**
     * Returns all related successful payments.
     *
     * @return MorphMany
     */
    public function successfulPayments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'payable')->successful();
    }

    /**
     * @param int $amount
     * @param CardType $cardType
     * @param string|null $description
     * @param Language|null $lang
     *
     * @return Payment
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Orkhanahmadov\Goldenpay\Exceptions\GoldenpayPaymentKeyException
     */
    public function createPayment(
        int $amount,
        CardType $cardType,
        ?string $description = null,
        ?Language $lang = null
    ): Payment {
        /** @var Goldenpay $goldenpay */
        $goldenpay = Container::getInstance()->make(Goldenpay::class);

        $payment = $goldenpay->payment($amount, $cardType, $description ?: $this->description(), $lang);

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
    abstract protected function description(): string;
}
