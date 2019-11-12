<?php

namespace Orkhanahmadov\LaravelGoldenpay\Tests\Feature;

use Carbon\Carbon;
use Orkhanahmadov\Goldenpay\Enums\CardType;
use Orkhanahmadov\Goldenpay\Enums\Language;
use Orkhanahmadov\LaravelGoldenpay\Models\Payment;
use Orkhanahmadov\LaravelGoldenpay\Tests\TestCase;

class GoldenpayTest extends TestCase
{
    public function testPaymentKeyMethodCreatesPaymentAndReturnsInstance()
    {
        $this->assertNull(Payment::first());

        $payment = $this->goldenpay->paymentKey(100, CardType::MASTERCARD(), 'item description');

        /** @see \Orkhanahmadov\LaravelGoldenpay\Tests\FakePaymentLibrary */
        $this->assertCount(1, Payment::all());
        $this->assertInstanceOf(Payment::class, $payment);
        $this->assertSame('valid-payment-key', $payment->payment_key);
        $this->assertSame(100, $payment->amount);
        $this->assertSame('m', $payment->card_type);
        $this->assertSame('en', $payment->language);
        $this->assertSame('item description', $payment->description);
        $this->assertSame(1, $payment->status);
        $this->assertEquals(0, $payment->checks);
    }

    public function testCreatesPaymentKeyWithDefinedLanguage()
    {
        $this->goldenpay->paymentKey(100, CardType::MASTERCARD(), 'something', Language::RU());

        $this->assertEquals('ru', Payment::first()->language);
    }

    public function testPaymentResult()
    {
        $payment = $this->goldenpay->paymentKey(2560, CardType::VISA(), 'some item', Language::AZ());
        $this->assertNull($payment->payment_date);
        $this->assertNull($payment->card_number);
        $this->assertNull($payment->reference_number);

        $result = $this->goldenpay->paymentResult($payment);

        /** @see \Orkhanahmadov\LaravelGoldenpay\Tests\FakePaymentLibrary@paymentResult */
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

    public function testPaymentResultCheckWithStringPaymentKey()
    {
        $this->markTestIncomplete();
    }
}
