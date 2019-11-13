<?php

namespace Orkhanahmadov\LaravelGoldenpay;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Orkhanahmadov\Goldenpay\Enums\CardType;
use Orkhanahmadov\Goldenpay\Enums\Language;
use Orkhanahmadov\Goldenpay\Goldenpay as Library;
use Orkhanahmadov\Goldenpay\PaymentInterface;
use Orkhanahmadov\LaravelGoldenpay\Actions\PaymentEvent;
use Orkhanahmadov\LaravelGoldenpay\Models\Payment;

class Goldenpay
{
    /**
     * @var Application
     */
    private $application;
    /**
     * @var PaymentEvent
     */
    private $event;
    /**
     * @var Library
     */
    private $goldenpay;

    /**
     * Goldenpay constructor.
     *
     * @param Application $application
     * @param Repository $config
     * @param PaymentEvent $event
     * @param PaymentInterface $goldenpay
     */
    public function __construct(
        Application $application,
        Repository $config,
        PaymentEvent $event,
        PaymentInterface $goldenpay
    ) {
        $this->application = $application;
        $this->event = $event;
        $this->goldenpay = $goldenpay;

        $this->goldenpay = $goldenpay->auth(
            $config->get('goldenpay.auth_key'),
            $config->get('goldenpay.merchant_name')
        );
    }

    /**
     * @param int $amount
     * @param CardType $cardType
     * @param string $description
     * @param Language|null $lang
     *
     * @throws \Orkhanahmadov\Goldenpay\Exceptions\GoldenpayPaymentKeyException
     *
     * @return Payment
     */
    public function payment(int $amount, CardType $cardType, string $description, ?Language $lang = null): Payment
    {
        $lang = $lang ?: $this->languageFromLocale();

        $paymentKey = $this->goldenpay->payment($amount, $cardType, $description, $lang);

        $payment = Payment::create([
            'payment_key' => $paymentKey->getPaymentKey(),
            'amount' => $amount,
            'card_type' => $cardType->getValue(),
            'language' => $lang->getValue(),
            'description' => $description,
        ]);

        $this->event->execute('goldenpay.payment_events.created', $payment);

        return $payment;
    }

    /**
     * @param Payment|string $payment
     *
     * @return Payment
     */
    public function result($payment): Payment
    {
        if (! $payment instanceof Payment) {
            $payment = Payment::wherePaymentKey($payment)->firstOrFail();
        }

        $result = $this->goldenpay->result($payment->payment_key);

        $payment->status = $result->getCode();
        $payment->message = $result->getMessage();
        $payment->reference_number = $result->getReferenceNumber();
        $payment->card_number = $result->getCardNumber();
        $payment->payment_date = $result->getPaymentDate();
        $payment->checks = $result->getCheckCount();
        $payment->save();

        $this->event->execute('goldenpay.payment_events.checked', $payment);

        return $payment;
    }

    /**
     * @return Language
     */
    private function languageFromLocale(): Language
    {
        $currentLocale = strtoupper($this->application->getLocale());

        if (! in_array($currentLocale, ['EN', 'RU', 'AZ'])) {
            return Language::EN();
        }

        return Language::{$currentLocale}();
    }
}
