<?php

namespace Orkhanahmadov\LaravelGoldenpay\Tests\Feature\Http;

use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
use Illuminate\Validation\ValidationException;
use Orkhanahmadov\Goldenpay\PaymentInterface;
use Orkhanahmadov\LaravelGoldenpay\Actions\PaymentEvent;
use Orkhanahmadov\LaravelGoldenpay\Goldenpay;
use Orkhanahmadov\LaravelGoldenpay\Http\Requests\Request;
use Orkhanahmadov\LaravelGoldenpay\Models\Payment;
use Orkhanahmadov\LaravelGoldenpay\Tests\FakePaymentLibrary;
use Orkhanahmadov\LaravelGoldenpay\Tests\FakeResultController;
use Orkhanahmadov\LaravelGoldenpay\Tests\TestCase;

class PaymentResultTest extends TestCase
{
    public function testSuccessfulPayment()
    {
        /** @var Payment $payment */
        $payment = factory(Payment::class)->create(['payment_key' => '1234-ABCD']);
        $this->assertFalse($payment->successful);

        $controller = new FakeResultController(
            new Request(['payment_key' => '1234-ABCD']),
            new PaymentEvent(config()),
            $this->goldenpay
        );
        $result = $controller->index();

        $this->assertSame(Response::HTTP_OK, $result->status());
        $payment->refresh();
        Event::assertDispatched(config('goldenpay.payment_events.successful'), function ($event) use ($payment) {
            return $event->payment->id === $payment->id;
        });
    }

    public function testFailedPayment()
    {
        $this->app->bind(PaymentInterface::class, function () {
            return new FakePaymentLibrary(5, 'failed');
        });
        $goldenpay = $this->app->make(Goldenpay::class);
        /** @var Payment $payment */
        $payment = factory(Payment::class)->create(['payment_key' => '1234-ABCD']);
        $this->assertFalse($payment->successful);

        $controller = new FakeResultController(
            new Request(['payment_key' => '1234-ABCD']),
            new PaymentEvent(config()),
            $goldenpay
        );
        $result = $controller->index();

        $this->assertSame(Response::HTTP_OK, $result->status());
        $payment->refresh();
        $this->assertFalse($payment->successful);
        Event::assertNotDispatched(config('goldenpay.payment_events.successful'));
    }

    public function testThrowsExceptionIfPaymentKeyQueryParameterDoesNotExist()
    {
        $this->expectException(ValidationException::class);

        $this->app->make(FakeResultController::class);
    }
}
