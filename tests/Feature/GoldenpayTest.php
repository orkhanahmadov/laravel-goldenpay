<?php

namespace Orkhanahmadov\LaravelGoldenpay\Tests\Feature;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Orkhanahmadov\Goldenpay\Enums\CardType;
use Orkhanahmadov\Goldenpay\Enums\Language;
use Orkhanahmadov\LaravelGoldenpay\Models\Payment;
use Orkhanahmadov\LaravelGoldenpay\Tests\TestCase;

class GoldenpayTest extends TestCase
{
    public function testPaymentMethodCreatesPaymentAndReturnsInstance()
    {
        $this->assertNull(Payment::first());

        $payment = $this->goldenpay->payment(100, CardType::MASTERCARD(), 'item description');

        /** @see \Orkhanahmadov\LaravelGoldenpay\Tests\FakePaymentLibrary::payment() */
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

    public function testCreatesPaymentMethodWithDefinedLanguage()
    {
        $this->goldenpay->payment(100, CardType::MASTERCARD(), 'something', Language::RU());

        $this->assertEquals('ru', Payment::first()->language);
    }

    public function testResultWithPaymentInstance()
    {
        $payment = $this->goldenpay->payment(2560, CardType::VISA(), 'some item', Language::AZ());
        $this->assertNull($payment->payment_date);
        $this->assertNull($payment->card_number);
        $this->assertNull($payment->reference_number);

        $result = $this->goldenpay->result($payment);

        /** @see \Orkhanahmadov\LaravelGoldenpay\Tests\FakePaymentLibrary::result() */
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

    public function testResultCheckWithStringPaymentKey()
    {
        $payment = $this->goldenpay->payment(2560, CardType::VISA(), 'some item', Language::AZ());

        $result = $this->goldenpay->result($payment->payment_key);

        $this->assertInstanceOf(Payment::class, $result);
    }

    public function testThrowsModelNotFoundExceptionIfPaymentKeyDoesNotExist()
    {
        $this->expectException(ModelNotFoundException::class);
        $this->expectExceptionMessage('No query results for model [Orkhanahmadov\LaravelGoldenpay\Models\Payment].');

        $this->goldenpay->result('non-existing-payment-key');
    }
}
