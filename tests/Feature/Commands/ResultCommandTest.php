<?php

namespace Orkhanahmadov\LaravelGoldenpay\Tests\Feature\Commands;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Orkhanahmadov\LaravelGoldenpay\Models\Payment;
use Orkhanahmadov\LaravelGoldenpay\Tests\TestCase;

class ResultCommandTest extends TestCase
{
    public function testChecksAllPendingPaymentsIfPaymentKeyNotPassed()
    {
        $payment1 = factory(Payment::class)->create(['created_at' => now()->subMinutes(10)]);
        $payment2 = factory(Payment::class)->create(['created_at' => now()->subMinutes(12)]);
        $payment3 = factory(Payment::class)->create(['status' => 1, 'created_at' => now()->subMinutes(5)]);
        $payment4 = factory(Payment::class)->create(['created_at' => now()->subMinutes(45)]);

        $this->artisan('goldenpay:result');

        $this->assertSame(1, $payment1->refresh()->status);
        $this->assertSame(1, $payment2->refresh()->status);
        $this->assertSame(1, $payment3->refresh()->status);
        $this->assertNull($payment4->refresh()->status);
        // todo: event count
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
        $this->expectException(ModelNotFoundException::class);
        $this->expectExceptionMessage('No query results for model [Orkhanahmadov\LaravelGoldenpay\Models\Payment].');

        $this->artisan('goldenpay:result ABC-123');
    }
}
