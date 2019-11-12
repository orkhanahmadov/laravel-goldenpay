<?php

namespace Orkhanahmadov\LaravelGoldenpay\Tests;

use Orkhanahmadov\Goldenpay\Enums\CardType;
use Orkhanahmadov\Goldenpay\Enums\Language;
use Orkhanahmadov\Goldenpay\PaymentInterface;
use Orkhanahmadov\Goldenpay\Response\PaymentKey;
use Orkhanahmadov\Goldenpay\Response\PaymentResult;

class FakePaymentLibrary implements PaymentInterface
{
    /**
     * Sets Goldenpay authentication credentials.
     *
     * @param string $authKey
     * @param string $merchantName
     *
     * @return self
     */
    public function auth(string $authKey, string $merchantName): PaymentInterface
    {
        return $this;
    }

    /**
     * Generates new payment key.
     *
     * @param int $amount
     * @param CardType $cardType
     * @param string $description
     * @param Language|null $lang
     *
     * @return PaymentKey
     */
    public function payment(int $amount, CardType $cardType, string $description, ?Language $lang): PaymentKey
    {
        return new PaymentKey(1, 'success', 'valid-payment-key');
    }

    /**
     * Checks result of payment using existing payment key.
     *
     * @param PaymentKey|string $paymentKey
     *
     * @return PaymentResult
     */
    public function result($paymentKey): PaymentResult
    {
        return new PaymentResult([
            'status' => [
                'code' => 1,
                'message' => 'success'
            ],
            'paymentKey' => $paymentKey,
            'merchantName' => 'merchant name',
            'amount' => 100,
            'checkCount' => 1,
            'paymentDate' => '2019-11-10 17:05:30',
            'cardNumber' => '123456******7890',
            'language' => 'lv',
            'description' => 'item-description',
            'rrn' => '12345678',
        ]);
    }
}
