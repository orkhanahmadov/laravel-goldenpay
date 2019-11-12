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
        $finishedPayment = factory(Payment::class)->create(['checks' => 5, 'status' => 33, 'created_at' => now()->subMinutes(31)]);
        $successfulPayment = factory(Payment::class)->create(['status' => 1, 'created_at' => now()->subMinutes(5)]);
        $manyCheckedPayment = factory(Payment::class)->create(['checks' => 5, 'created_at' => now()->subMinutes(40)]);
        $pendingPayment1 = factory(Payment::class)->create(['status' => 55, 'created_at' => now()->subMinutes(10)]);
        $pendingPayment2 = factory(Payment::class)->create(['created_at' => now()->subMinutes(12)]);
        $pendingPayment3 = factory(Payment::class)->create(['checks' => 4, 'created_at' => now()->subMinutes(45)]);
        $pendingPayment4 = factory(Payment::class)->create(['checks' => 2, 'status' => 33, 'created_at' => now()->subMinutes(35)]);

        $payments = Payment::pending()->get();

        $this->assertTrue($payments->contains($pendingPayment1));
        $this->assertTrue($payments->contains($pendingPayment2));
        $this->assertTrue($payments->contains($pendingPayment3));
        $this->assertTrue($payments->contains($pendingPayment4));
        $this->assertFalse($payments->contains($successfulPayment));
        $this->assertFalse($payments->contains($finishedPayment));
        $this->assertFalse($payments->contains($manyCheckedPayment));
    }
}
