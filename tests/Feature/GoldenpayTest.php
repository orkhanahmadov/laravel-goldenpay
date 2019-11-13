<?php

namespace Orkhanahmadov\LaravelGoldenpay\Tests\Feature;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Orkhanahmadov\Goldenpay\Enums\CardType;
use Orkhanahmadov\Goldenpay\Enums\Language;
use Orkhanahmadov\Goldenpay\PaymentInterface;
use Orkhanahmadov\LaravelGoldenpay\Goldenpay;
use Orkhanahmadov\LaravelGoldenpay\Models\Payment;
use Orkhanahmadov\LaravelGoldenpay\Tests\FakePaymentLibrary;
use Orkhanahmadov\LaravelGoldenpay\Tests\TestCase;

class GoldenpayTest extends TestCase
{
    public function testPaymentMethodCreatesPaymentAndReturnsInstance()
    {
        $this->assertNull(Payment::first());

        $payment = $this->goldenpay->payment(100, CardType::MASTERCARD(), 'item description');

        $this->assertCount(1, Payment::all());
        $this->assertInstanceOf(Payment::class, $payment);
        /* @see \Orkhanahmadov\LaravelGoldenpay\Tests\FakePaymentLibrary::payment() */
        $this->assertNull($payment->status);
        $this->assertNull($payment->message);
        $this->assertSame('valid-payment-key', $payment->payment_key);
        $this->assertSame(100, $payment->amount);
        $this->assertSame('m', $payment->card_type);
        $this->assertSame('en', $payment->language);
        $this->assertSame('item description', $payment->description);
        $this->assertEquals(0, $payment->checks);
    }

    public function testPaymentMethodFiresPaymentCreatedEvent()
    {
        $payment = $this->goldenpay->payment(100, CardType::MASTERCARD(), 'whatever');

        Event::assertDispatched(config('goldenpay.payment_events.created'), function ($event) use ($payment) {
            return $event->payment->id === $payment->id;
        });
    }

    public function testCreatesPaymentMethodWithDefinedLanguage()
    {
        $this->goldenpay->payment(100, CardType::MASTERCARD(), 'something', Language::RU());

        $this->assertEquals('ru', Payment::first()->language);
    }

    public function testLanguageDefaultsToEnglishIfApplicationLocateIsNotSupported()
    {
        $this->app->setLocale('es');

        $this->goldenpay->payment(100, CardType::MASTERCARD(), 'something');

        $this->assertEquals('en', Payment::first()->language);
    }

    public function testResultWithPaymentInstance()
    {
        $payment = $this->goldenpay->payment(2560, CardType::VISA(), 'some item', Language::AZ());
        $this->assertNull($payment->payment_date);
        $this->assertNull($payment->card_number);
        $this->assertNull($payment->reference_number);

        $result = $this->goldenpay->result($payment);

        $this->assertInstanceOf(Payment::class, $result);
        /* @see \Orkhanahmadov\LaravelGoldenpay\Tests\FakePaymentLibrary::result() */
        $this->assertSame(1, $result->status);
        $this->assertSame('success', $result->message);
        $this->assertSame(2560, $result->amount);
        $this->assertSame(1, $result->checks);
        $this->assertInstanceOf(Carbon::class, $result->payment_date);
        $this->assertSame('2019-11-10 17:05:30', $result->payment_date->format('Y-m-d H:i:s'));
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

    public function testResultMethodFiresPaymentCheckedEvent()
    {
        $payment = factory(Payment::class)->create();

        $result = $this->goldenpay->result($payment);

        Event::assertDispatched(config('goldenpay.payment_events.checked'), function ($event) use ($result) {
            return $event->payment->id === $result->id;
        });
    }

    public function testResultMethodFiresPaymentSuccessfulEventIfPaymentIsSuccessful()
    {
        $payment = factory(Payment::class)->create();

        $result = $this->goldenpay->result($payment);

        Event::assertDispatched(config('goldenpay.payment_events.successful'), function ($event) use ($result) {
            return $event->payment->id === $result->id && $result->successful;
        });
    }

    public function testResultMethodWontFiresPaymentSuccessfulEventIfPaymentIsNotSuccessful()
    {
        $this->app->bind(PaymentInterface::class, function () {
            return new FakePaymentLibrary(5, 'failed');
        });
        $goldenpay = $this->app->make(Goldenpay::class);
        $payment = factory(Payment::class)->create();

        $goldenpay->result($payment);

        Event::assertNotDispatched(config('goldenpay.payment_events.successful'));
    }

    public function testThrowsModelNotFoundExceptionIfPaymentKeyDoesNotExist()
    {
        $this->expectException(ModelNotFoundException::class);
        $this->expectExceptionMessage('No query results for model [Orkhanahmadov\LaravelGoldenpay\Models\Payment].');

        $this->goldenpay->result('non-existing-payment-key');
    }

    public function testThrowsInvalidArgumentExceptionIfAuthKeyOrMerchantNameIsNotSet()
    {
        Config::set('goldenpay.auth_key', null);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Missing "auth_key" and/or "merchant_name" parameters. Make sure to set them in config or .env file'
        );

        $this->goldenpay->payment(100, CardType::VISA(), 'whatever');
    }
}
