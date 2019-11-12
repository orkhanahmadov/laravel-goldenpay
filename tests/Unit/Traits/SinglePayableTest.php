<?php

namespace Orkhanahmadov\LaravelGoldenpay\Tests\Unit\Traits;

use Illuminate\Support\Facades\DB;
use Orkhanahmadov\LaravelGoldenpay\Models\Payment;
use Orkhanahmadov\LaravelGoldenpay\Tests\FakeModel;
use Orkhanahmadov\LaravelGoldenpay\Tests\TestCase;

class SinglePayableTest extends TestCase
{
    public function testHasOnePayment()
    {
        $model = factory(FakeModel::class)->create();
        $payment = factory(Payment::class)->create([
            'payable_id' => $model->id,
            'payable_type' => FakeModel::class
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

        DB::statement('CREATE TABLE fake_models (id INT, name VARCHAR);');
    }
}
