<?php

namespace Orkhanahmadov\LaravelGoldenpay;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Orkhanahmadov\Goldenpay\Enums\CardType;
use Orkhanahmadov\Goldenpay\Enums\Language;
use Orkhanahmadov\Goldenpay\Goldenpay as Library;
use Orkhanahmadov\Goldenpay\PaymentInterface;
use Orkhanahmadov\Goldenpay\PaymentKey;
use Orkhanahmadov\Goldenpay\PaymentResult;
use Orkhanahmadov\LaravelGoldenpay\Models\Payment;

class Goldenpay
{
    /**
     * @var Application
     */
    private $application;
    /**
     * @var Library
     */
    private $goldenpay;

    /**
     * Goldenpay constructor.
     *
     * @param Application $application
     * @param Repository $config
     * @param PaymentInterface $goldenpay
     */
    public function __construct(Application $application, Repository $config, PaymentInterface $goldenpay)
    {
        $this->application = $application;
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
     * @return PaymentKey
     */
    public function paymentKey(int $amount, CardType $cardType, string $description, ?Language $lang = null): PaymentKey
    {
        $lang = $lang ?: $this->languageFromLocale();

        $paymentKey = $this->goldenpay->paymentKey($amount, $cardType, $description, $lang);

        Payment::create([
            'payment_key' => $paymentKey->getKey(),
            'amount' => $amount,
            'card_type' => $cardType->getValue(),
            'language' => $lang->getValue(),
            'description' => $description,
        ]);

        return $paymentKey;
    }

    public function paymentResult($paymentKey): PaymentResult
    {
        return $this->goldenpay->paymentResult($paymentKey);
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
