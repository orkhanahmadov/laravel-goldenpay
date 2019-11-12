<?php

namespace Orkhanahmadov\LaravelGoldenpay\Tests\Unit\Traits;

use Illuminate\Support\Facades\DB;
use Orkhanahmadov\Goldenpay\Enums\CardType;
use Orkhanahmadov\LaravelGoldenpay\Models\Payment;
use Orkhanahmadov\LaravelGoldenpay\Tests\FakePayableModel;
use Orkhanahmadov\LaravelGoldenpay\Tests\TestCase;

class PayableTest extends TestCase
{
    public function testHasManyPayments()
    {
        $model = factory(FakePayableModel::class)->create();
        $payment1 = factory(Payment::class)->create([
            'payable_id' => $model->id,
            'payable_type' => FakePayableModel::class
        ]);
        $payment2 = factory(Payment::class)->create([
            'payable_id' => $model->id,
            'payable_type' => FakePayableModel::class
        ]);

        $this->assertCount(2, $model->payments);
        $this->assertSame($payment1->id, $model->payments->first()->id);
        $this->assertSame($payment2->id, $model->payments->last()->id);
    }

    public function testCreatesPayment()
    {
        /** @var FakePayableModel $model */
        $model = factory(FakePayableModel::class)->create(['name' => 'my description']);

        $payment = $model->createPayment(1550, CardType::MASTERCARD());

        $this->assertInstanceOf(Payment::class, $payment);
        $this->assertSame($model->id, $payment->payable_id);
        $this->assertSame(FakePayableModel::class, $payment->payable_type);
        $this->assertSame('valid-payment-key', $payment->payment_key);
        $this->assertSame(1550, $payment->amount);
        $this->assertSame('m', $payment->card_type);
        $this->assertSame('my description', $payment->description);
        $this->assertSame('en', $payment->language);
    }

    public function testCreatesPaymentWithCustomDescription()
    {
        $model = factory(FakePayableModel::class)->create(['name' => 'my description']);

        $payment = $model->createPayment(1550, CardType::MASTERCARD(), 'custom description');

        $this->assertInstanceOf(Payment::class, $payment);
        $this->assertSame($model->id, $payment->payable_id);
        $this->assertSame(FakePayableModel::class, $payment->payable_type);
        $this->assertSame('custom description', $payment->description);
    }

    protected function setUp(): void
    {
        parent::setUp();

        DB::statement('CREATE TABLE fake_payable_models (id INT, name VARCHAR);');
    }
}
