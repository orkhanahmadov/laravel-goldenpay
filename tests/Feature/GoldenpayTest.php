<?php

namespace Orkhanahmadov\LaravelGoldenpay\Tests\Feature;

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

        $paymentKey = $this->goldenpay->paymentKey(100, 'm', 'something');

        $this->assertSame('valid-payment-key', $paymentKey->getKey());
        $this->assertSame(1, $paymentKey->getCode());
        $this->assertSame('success', $paymentKey->getMessage());
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

    protected function setUp(): void
    {
        parent::setUp();

        $this->goldenpay = $this->app->make(Goldenpay::class);
    }
}
