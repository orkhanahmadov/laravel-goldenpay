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
     * @var int
     */
    private $code;
    /**
     * @var string
     */
    private $message;
    /**
     * @var array|string|null
     */
    private $data = null;

    /**
     * FakePaymentLibrary constructor.
     *
     * @param int $code
     * @param string $message
     * @param array|string|null $data
     */
    public function __construct(int $code = 1, string $message = 'success', $data = null)
    {
        $this->code = $code;
        $this->message = $message;
        $this->data = $data;
    }

    /**
     * Sets Goldenpay authentication credentials.
     *
     * @param string $authKey
     * @param string $merchantName
     *
     * @return self
     */
    public function authenticate(string $authKey, string $merchantName): PaymentInterface
    {
        return $this;
    }

    /**
     * Gets new payment key from Goldenpay.
     *
     * @param int $amount
     * @param CardType $cardType
     * @param string $description
     * @param Language $lang
     *
     * @return PaymentKey
     */
    public function payment(int $amount, CardType $cardType, string $description, Language $lang): PaymentKey
    {
        return new PaymentKey(
            $this->data ?: 'valid-payment-key',
            $this->code,
            $this->message
        );
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
        return new PaymentResult(
            $this->data ?: [
                'paymentKey' => $paymentKey,
                'merchantName' => 'merchant name',
                'amount' => 100,
                'checkCount' => 1,
                'paymentDate' => '2019-11-10 17:05:30',
                'cardNumber' => '123456******7890',
                'language' => 'lv',
                'description' => 'item-description',
                'rrn' => '12345678',
            ],
            $this->code,
            $this->message
        );
    }
}
