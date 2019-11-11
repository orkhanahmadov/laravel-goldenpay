<?php

namespace Orkhanahmadov\LaravelGoldenpay\Tests\Feature;

use Carbon\Carbon;
use Orkhanahmadov\Goldenpay\Enums\CardType;
use Orkhanahmadov\Goldenpay\Enums\Language;
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

        $payment = $this->goldenpay->paymentKey(100, CardType::MASTERCARD(), 'something');

        $this->assertCount(1, Payment::all());
        $this->assertInstanceOf(Payment::class, $payment);
        $this->assertSame('valid-payment-key', $payment->payment_key);
        $this->assertSame(100, $payment->amount);
        $this->assertSame('m', $payment->card_type);
        $this->assertSame('en', $payment->language);
        $this->assertSame('something', $payment->description);
        $this->assertSame(1, $payment->status);
        $this->assertEquals(0, $payment->checks);
    }

    public function testPaymentKeyWithDefinedLanguage()
    {
        $this->goldenpay->paymentKey(100, CardType::MASTERCARD(), 'something', Language::RU());

        $this->assertEquals('ru', Payment::first()->language);
    }

    public function testPaymentResult()
    {
        $payment = $this->goldenpay->paymentKey(2560, CardType::VISA(), 'some item', Language::AZ());

        $result = $this->goldenpay->paymentResult($payment);

        $this->assertInstanceOf(Payment::class, $result);
        $this->assertSame(1, $result->status);
        $this->assertSame('success', $result->message);
        $this->assertSame(2560, $result->amount);
        $this->assertSame(1, $result->checks);
        $this->assertInstanceOf(Carbon::class, $result->payment_date);
        $this->assertSame('123456******7890', $result->card_number);
        $this->assertSame('lv', $result->language);
        $this->assertSame('some item', $result->description);
        $this->assertSame('12345678', $result->reference_number);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->goldenpay = $this->app->make(Goldenpay::class);
    }
}
