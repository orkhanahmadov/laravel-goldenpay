<?php

namespace Orkhanahmadov\LaravelGoldenpay\Tests\Feature\Commands;

use Carbon\Carbon;
use Orkhanahmadov\LaravelGoldenpay\Models\Payment;
use Orkhanahmadov\LaravelGoldenpay\Tests\TestCase;

class ResultCommandTest extends TestCase
{
    public function testChecksAllPendingPaymentsIfPaymentKeyNotPassed()
    {
        $this->markTestIncomplete();
    }

    public function testChecksGivenPaymentWithPaymentKey()
    {
        /** @var Payment $payment */
        $payment = factory(Payment::class)->create(['payment_key' => 'ABC-123']);

        $this->artisan('goldenpay:result ABC-123');

        $payment->refresh();
        /** @see \Orkhanahmadov\LaravelGoldenpay\Tests\FakePaymentLibrary::result() */
        $this->assertSame(1, $payment->status);
        $this->assertSame('success', $payment->message);
        $this->assertSame(1, $payment->checks);
        $this->assertInstanceOf(Carbon::class, $payment->payment_date);
        $this->assertSame('2019-11-10 17:05:30', $payment->payment_date->format('Y-m-d H:i:s'));
        $this->assertSame('123456******7890', $payment->card_number);
        $this->assertSame('12345678', $payment->reference_number);
    }

    public function testThrowsModelNotFoundExceptionIfNonExistingPaymentKeyPassed()
    {
        $this->markTestIncomplete();
    }
}
