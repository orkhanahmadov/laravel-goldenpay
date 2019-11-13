<?php

namespace Orkhanahmadov\LaravelGoldenpay\Tests\Unit\Actions;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Orkhanahmadov\LaravelGoldenpay\Actions\PaymentEvent;
use Orkhanahmadov\LaravelGoldenpay\Models\Payment;
use Orkhanahmadov\LaravelGoldenpay\Tests\TestCase;

class PaymentEventTest extends TestCase
{
    /**
     * @var PaymentEvent
     */
    private $action;
    /**
     * @var Payment
     */
    private $payment;

    public function testFiresPaymentEventWithEventNameAndPaymentInstance()
    {
        $this->action->execute('goldenpay.events.payment_created', $this->payment);

        Event::assertDispatched(config('goldenpay.events.payment_created'), function ($event) {
            return $event->payment->id === $this->payment->id;
        });
    }

    public function testWontFireAnyEventIfConfigDisablesPaymentEvents()
    {
        Config::set('goldenpay.events.enabled', false);
        $action = $this->app->make(PaymentEvent::class);

        $action->execute('goldenpay.events.payment_created', $this->payment);

        Event::assertNotDispatched(config('goldenpay.events.payment_created'));
    }

    public function testWontFireSpecificEventIfItSetToNullInConfig()
    {
        Config::set('goldenpay.events.payment_created', null);

        $this->action->execute('goldenpay.events.payment_created', $this->payment);

        Event::assertNotDispatched(config('goldenpay.events.payment_created'));
    }

    public function testThrowsExceptionIfEventIsNotInstanceOfPaymentEvent()
    {
        $this->markTestIncomplete();
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->action = $this->app->make(PaymentEvent::class);
        $this->payment = factory(Payment::class)->create();
    }
}
