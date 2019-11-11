<?php

namespace Orkhanahmadov\LaravelGoldenpay\Tests\Feature\Http;

use Orkhanahmadov\LaravelGoldenpay\Models\Payment;
use Orkhanahmadov\LaravelGoldenpay\Tests\TestCase;

class PaymentResultTest extends TestCase
{
    public function testSuccessful()
    {
        $this->withoutExceptionHandling();
        factory(Payment::class)->create(['payment_key' => '1234-ABCD']);

        $result = $this->get('goldenpay/success?payment_key=1234-ABCD');

        $result->assertOk();
    }
}
