<?php

namespace Orkhanahmadov\LaravelGoldenpay;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Orkhanahmadov\Goldenpay\Goldenpay as Library;
use Orkhanahmadov\Goldenpay\PaymentInterface;
use Orkhanahmadov\Goldenpay\PaymentKey;
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
     * @param string $cardType
     * @param string $description
     * @param string|null $language
     *
     * @throws \Orkhanahmadov\Goldenpay\Exceptions\GoldenpayPaymentKeyException
     *
     * @return PaymentKey
     */
    public function paymentKey(int $amount, string $cardType, string $description, ?string $language = null): PaymentKey
    {
        $language = $language ?: $this->languageFromLocale();

        $paymentKey = $this->goldenpay->paymentKey($amount, $cardType, $description, $language);

        Payment::create([
            'payment_key' => $paymentKey->getKey(),
            'amount' => $amount,
            'card_type' => $cardType,
            'language' => $language,
            'description' => $description,
        ]);

        return $paymentKey;
    }

    /**
     * @return string
     */
    private function languageFromLocale()
    {
        $currentLocale = $this->application->getLocale();

        if (! in_array($currentLocale, ['en', 'ru', 'az'])) {
            return 'en';
        }

        return $currentLocale === 'az' ? 'lv' : $currentLocale;
    }
}
