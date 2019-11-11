<?php

namespace Orkhanahmadov\LaravelGoldenpay\Tests\Feature;

use Orkhanahmadov\Goldenpay\Enums\CardType;
use Orkhanahmadov\Goldenpay\Enums\Language;
use Orkhanahmadov\Goldenpay\PaymentKey;
use Orkhanahmadov\Goldenpay\PaymentResult;
use Orkhanahmadov\LaravelGoldenpay\Goldenpay;
use Orkhanahmadov\LaravelGoldenpay\Models\Payment;
use Orkhanahmadov\LaravelGoldenpay\Tests\TestCase;

class GoldenpayTest extends TestCase
{
    /**
     * @var Goldenpay
     */
    private $goldenpay;

    public function testPaymentKey()
    {
        $this->assertNull(Payment::first());

        $paymentKey = $this->goldenpay->paymentKey(100, CardType::MASTERCARD(), 'something');

        $this->assertInstanceOf(PaymentKey::class, $paymentKey);
        $this->assertEquals('valid-payment-key', $paymentKey->getKey());
        $this->assertEquals(1, $paymentKey->getCode());
        $this->assertEquals('success', $paymentKey->getMessage());
        $this->assertCount(1, Payment::all());
        $this->assertNotNull($payment = Payment::first());
        $this->assertEquals('valid-payment-key', $payment->payment_key);
        $this->assertEquals(100, $payment->amount);
        $this->assertEquals('m', $payment->card_type);
        $this->assertEquals('en', $payment->language);
        $this->assertEquals('something', $payment->description);
        $this->assertNull($payment->status);
        $this->assertEquals(0, $payment->checks);
    }

    public function testPaymentKeyWithDefinedLanguage()
    {
        $this->goldenpay->paymentKey(100, CardType::MASTERCARD(), 'something', Language::RU());

        $this->assertEquals('ru', Payment::first()->language);
    }

    public function testPaymentResult()
    {
        $this->assertCount(0, Payment::all());
        $this->assertNull(Payment::first());



        $result = $this->goldenpay->paymentResult('valid-payment-key');

        $this->assertInstanceOf(PaymentResult::class, $result);

    }

    public function testDemo()
    {
//        $paymentKey = $this->goldenpay->paymentKey(100, CardType::MASTERCARD(), 'test item');

//        dd($paymentKey->paymentUrl());

        // https://rest.goldenpay.az/web/paypage?payment_key=47365534-7cce-492c-a1cf-4ebe41439823

        $result = $this->goldenpay->paymentResult('47365534-7cce-492c-a1cf-4ebe41439823');

        dd($result);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->goldenpay = $this->app->make(Goldenpay::class);
    }
}
