<?php

namespace Orkhanahmadov\LaravelGoldenpay\Tests\Feature\Http;

use Carbon\Carbon;
use Illuminate\Support\Facades\Event;
use Orkhanahmadov\LaravelGoldenpay\Models\Payment;
use Orkhanahmadov\LaravelGoldenpay\Tests\TestCase;

class PaymentResultTest extends TestCase
{
    public function testSuccessfulPayment()
    {
        $this->withoutExceptionHandling();
        Event::fake();
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
        Event::assertDispatched(config('goldenpay.events.payment_successful'), function ($event) use ($payment) {
            return $event->payment->id === $payment->id && $event->payment->status === 1;
        });
        Event::assertNotDispatched(config('goldenpay.events.payment_failed'));
    }

    public function testFailedPayment()
    {
        $this->markTestIncomplete();
    }
}
