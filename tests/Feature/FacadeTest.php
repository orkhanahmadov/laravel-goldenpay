<?php

namespace Orkhanahmadov\LaravelGoldenpay\Tests\Feature;

use Orkhanahmadov\Goldenpay\Enums\CardType;
use Orkhanahmadov\LaravelGoldenpay\Models\Payment;
use Orkhanahmadov\LaravelGoldenpay\Tests\TestCase;

class FacadeTest extends TestCase
{
    public function testFacadeWithFake()
    {
        /** @var Payment $payment */
        $payment = \Goldenpay::payment(1000, CardType::VISA(), 'item desc');

        $this->assertInstanceOf(Payment::class, $payment);
        $this->assertSame(1000, $payment->amount);
        $this->assertSame('v', $payment->card_type);
        $this->assertSame('item desc', $payment->description);
    }
}
