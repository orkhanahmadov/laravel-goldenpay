<?php

namespace Orkhanahmadov\LaravelGoldenpay\Tests\Feature\Http;

use Carbon\Carbon;
use Illuminate\Support\Facades\Event;
use Orkhanahmadov\Goldenpay\PaymentInterface;
use Orkhanahmadov\LaravelGoldenpay\Models\Payment;
use Orkhanahmadov\LaravelGoldenpay\Tests\FakePaymentLibrary;
use Orkhanahmadov\LaravelGoldenpay\Tests\TestCase;

class PaymentResultTest extends TestCase
{
    public function testSuccessfulPayment()
    {
        /** @var Payment $payment */
        $payment = factory(Payment::class)->create(['payment_key' => '1234-ABCD']);
        $this->assertNull($payment->status);

        $result = $this->get('__goldenpay-fake-test-route__?payment_key=1234-ABCD');

        $result->assertOk();
        $payment->refresh();
        /** @see \Orkhanahmadov\LaravelGoldenpay\Tests\FakePaymentLibrary::result() */
        $this->assertSame(1, $payment->checks);
        $this->assertInstanceOf(Carbon::class, $payment->payment_date);
        $this->assertSame('2019-11-10 17:05:30', $payment->payment_date->format('Y-m-d H:i:s'));
        $this->assertSame('123456******7890', $payment->card_number);
        $this->assertSame('12345678', $payment->reference_number);
        Event::assertDispatched(config('goldenpay.payment_events.successful'), function ($event) use ($payment) {
            return $event->payment->id === $payment->id && $event->payment->successful;
        });
        Event::assertNotDispatched(config('goldenpay.payment_events.failed'));
    }

    public function testFailedPayment()
    {
        $this->app->bind(PaymentInterface::class, function () {
            return new FakePaymentLibrary(5, 'failed');
        });
        /** @var Payment $payment */
        $payment = factory(Payment::class)->create(['payment_key' => '1234-ABCD']);
        $this->assertNull($payment->status);

        $result = $this->get('__goldenpay-fake-test-route__?payment_key=1234-ABCD');

        $result->assertOk();
        $payment->refresh();
        Event::assertDispatched(config('goldenpay.payment_events.failed'), function ($event) use ($payment) {
            return $event->payment->id === $payment->id && !$event->payment->successful;
        });
        Event::assertNotDispatched(config('goldenpay.payment_events.successful'));
    }
}
