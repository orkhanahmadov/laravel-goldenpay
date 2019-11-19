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
        return $this->morphMany(Payment::class, 'payable')->whereSuccessful();
    }

    /**
     * @param CardType $cardType
     * @param int|null $amount
     * @param string|null $description
     * @param Language|null $lang
     *
     * @return Payment
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Orkhanahmadov\Goldenpay\Exceptions\GoldenpayPaymentKeyException
     */
    public function createPayment(
        CardType $cardType,
        ?int $amount = null,
        ?string $description = null,
        ?Language $lang = null
    ): Payment {
        /** @var Goldenpay $goldenpay */
        $goldenpay = Container::getInstance()->make(Goldenpay::class);

        $payment = $goldenpay->payment(
            $amount ?: $this->amount(),
            $cardType,
            $description ?: $this->description(),
            $lang
        );

        $payment->payable_type = self::class;
        $payment->payable_id = $this->getKey();
        $payment->save();

        return $payment;
    }

    /**
     * Defines payment amount for this model's payments.
     *
     * @return int
     */
    abstract protected function amount(): int;

    /**
     * Defines description for this model's payments.
     *
     * @return string
     */
    abstract protected function description(): string;
}
