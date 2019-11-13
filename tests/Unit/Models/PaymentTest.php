<?php

namespace Orkhanahmadov\LaravelGoldenpay\Tests\Unit\Models;

use Orkhanahmadov\Goldenpay\Response\PaymentKey;
use Orkhanahmadov\LaravelGoldenpay\Models\Payment;
use Orkhanahmadov\LaravelGoldenpay\Tests\FakePayableModel;
use Orkhanahmadov\LaravelGoldenpay\Tests\TestCase;

class PaymentTest extends TestCase
{
    public function testBelongsToPayable()
    {
        $model = factory(FakePayableModel::class)->create();
        $payment = factory(Payment::class)->create([
            'payable_type' => FakePayableModel::class,
            'payable_id' => $model->id,
        ]);

        $this->assertInstanceOf(FakePayableModel::class, $payment->payable);
        $this->assertSame($model->id, $payment->payable->id);
    }

    public function testPaymentUrlAttributeReturnsStringIfPaymentIsNotSuccessful()
    {
        $payment = factory(Payment::class)->create(['payment_key' => 'new_payment_key']);

        $expected = PaymentKey::PAYMENT_PAGE.'new_payment_key';

        $this->assertSame($expected, $payment->payment_url);
    }

    public function testPaymentUrlAttributeReturnsNullIfPaymentIsNotSuccessful()
    {
        $payment = factory(Payment::class)->create(['status' => 1]);

        $this->assertNull($payment->payment_url);
    }

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

    public function testSuccessfulScope()
    {
        $successfulPayment = factory(Payment::class)->create(['status' => 1]);
        $failedPayment1 = factory(Payment::class)->create(['status' => 5]);
        $failedPayment2 = factory(Payment::class)->create(['status' => null]);

        $payments = Payment::successful()->get();

        $this->assertTrue($payments->contains($successfulPayment));
        $this->assertFalse($payments->contains($failedPayment1));
        $this->assertFalse($payments->contains($failedPayment2));
    }
}
