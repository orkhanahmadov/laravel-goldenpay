<?php

namespace Orkhanahmadov\LaravelGoldenpay\Tests;

use Orkhanahmadov\Goldenpay\PaymentInterface;
use Orkhanahmadov\Goldenpay\PaymentKey;
use Orkhanahmadov\Goldenpay\PaymentResult;

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
     * @param string $cardType
     * @param string $description
     * @param string $lang
     *
     * @return PaymentKey
     */
    public function paymentKey(int $amount, string $cardType, string $description, string $lang): PaymentKey
    {
        return new PaymentKey(1, 'valid message', 'valid-payment-key');
    }

    /**
     * Checks result of payment using existing payment key.
     *
     * @param PaymentKey|string $paymentKey
     *
     * @return PaymentResult
     */
    public function paymentResult($paymentKey): PaymentResult
    {
        return new PaymentResult([

        ]);
    }
}
