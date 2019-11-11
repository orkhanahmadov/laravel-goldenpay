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
}
