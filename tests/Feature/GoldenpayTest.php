<?php

namespace Orkhanahmadov\LaravelGoldenpay\Tests\Feature;

use Orkhanahmadov\LaravelGoldenpay\Goldenpay;
use Orkhanahmadov\LaravelGoldenpay\Models\Payment;
use Orkhanahmadov\LaravelGoldenpay\Tests\TestCase;

class GoldenpayTest extends TestCase
{
    /**
     * @var Goldenpay
     */
    private $goldenpay;

    public function testPaymentKey()
    {
        $this->assertNull(Payment::first());

        $this->goldenpay->paymentKey(100, 'm', 'something');

        $this->assertCount(1, Payment::all());
        $this->assertNotNull($payment = Payment::first());
        $this->assertSame(100, $payment->amount);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->goldenpay = $this->app->make(Goldenpay::class);
    }
}
