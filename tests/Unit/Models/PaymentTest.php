<?php

namespace Orkhanahmadov\LaravelGoldenpay\Tests\Unit\Models;

use Orkhanahmadov\LaravelGoldenpay\Models\Payment;
use Orkhanahmadov\LaravelGoldenpay\Tests\TestCase;

class PaymentTest extends TestCase
{
    public function testFormattedAmountAttribute()
    {
        $payment = factory(Payment::class)->create(['amount' => 1569]);

        $this->assertSame(15.69, $payment->formatted_amount);
    }

    public function testSuccessfulAttribute()
    {
        $successfulPayment = factory(Payment::class)->create(['status' => 1]);
        $this->assertTrue($successfulPayment->successful);

        $failedPayment = factory(Payment::class)->create(['status' => 2]);
        $this->assertFalse($failedPayment->successful);
    }

    public function testPendingScope()
    {
        $finishedPayment = factory(Payment::class)->create(['created_at' => now()->subMinutes(31)]);
        $pendingPayment = factory(Payment::class)->create(['created_at' => now()->subMinutes(10)]);

        $payments = Payment::pending()->get();

        $this->assertTrue($payments->contains($pendingPayment));
        $this->assertFalse($payments->contains($finishedPayment));
    }
}
