<?php

namespace Orkhanahmadov\LaravelGoldenpay\Tests\Unit\Traits;

use Illuminate\Support\Facades\DB;
use Orkhanahmadov\Goldenpay\Enums\CardType;
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
        /** @var FakeSinglePayable $model */
        $model = factory(FakeSinglePayable::class)->create(['name' => 'my description']);

        $payment = $model->createPayment(1550, CardType::MASTERCARD());

        $this->assertInstanceOf(Payment::class, $payment);
        $this->assertSame($model->id, $payment->payable_id);
        $this->assertSame(FakeSinglePayable::class, $payment->payable_type);
        $this->assertSame('valid-payment-key', $payment->payment_key);
        $this->assertSame(1550, $payment->amount);
        $this->assertSame('m', $payment->card_type);
        $this->assertSame('my description', $payment->description);
        $this->assertSame('en', $payment->language);
    }

    public function testCreatesPaymentWithCustomDescription()
    {
        $model = factory(FakeSinglePayable::class)->create(['name' => 'my description']);

        $payment = $model->createPayment(1550, CardType::MASTERCARD(), 'custom description');

        $this->assertInstanceOf(Payment::class, $payment);
        $this->assertSame($model->id, $payment->payable_id);
        $this->assertSame(FakeSinglePayable::class, $payment->payable_type);
        $this->assertSame('custom description', $payment->description);
    }

    protected function setUp(): void
    {
        parent::setUp();

        DB::statement('CREATE TABLE fake_single_payables (id INT, name VARCHAR);');
    }
}
