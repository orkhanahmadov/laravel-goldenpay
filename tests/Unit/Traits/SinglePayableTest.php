<?php

namespace Orkhanahmadov\LaravelGoldenpay\Tests\Unit\Traits;

use Illuminate\Support\Facades\DB;
use Orkhanahmadov\LaravelGoldenpay\Models\Payment;
use Orkhanahmadov\LaravelGoldenpay\Tests\FakeSinglePayable;
use Orkhanahmadov\LaravelGoldenpay\Tests\TestCase;

class SinglePayableTest extends TestCase
{
    public function testHasOnePayment()
    {
        $model = factory(FakeSinglePayable::class)->create();
        $payment = factory(Payment::class)->create([
            'payable_id' => $model->id,
            'payable_type' => FakeSinglePayable::class
        ]);

        $this->assertInstanceOf(Payment::class, $model->payment);
        $this->assertSame($payment->id, $model->payment->id);
    }

    public function testCreatesPayment()
    {
        $this->markTestIncomplete();
    }

    protected function setUp(): void
    {
        parent::setUp();

        DB::statement('CREATE TABLE fake_single_payables (id INT, name VARCHAR);');
    }
}
