<?php

namespace Orkhanahmadov\LaravelGoldenpay\Http\Controllers;

use Orkhanahmadov\LaravelGoldenpay\Actions\PaymentEvent;
use Orkhanahmadov\LaravelGoldenpay\Goldenpay;
use Orkhanahmadov\LaravelGoldenpay\Http\Requests\Request;
use Orkhanahmadov\LaravelGoldenpay\Models\Payment;

abstract class GoldenpayController
{
    /**
     * @var PaymentEvent
     */
    private $event;
    /**
     * @var Goldenpay
     */
    protected $goldenpay;
    /**
     * @var Payment
     */
    protected $payment;


    /**
     * Controller constructor.
     *
     * @param Request $request
     * @param PaymentEvent $event
     * @param Goldenpay $goldenpay
     */
    public function __construct(
        Request $request,
        PaymentEvent $event,
        Goldenpay $goldenpay
    ) {
        $this->event = $event;
        $this->goldenpay = $goldenpay;

        $this->checkPaymentResult($request);
    }

    /**
     * Checks payment result with "payment_key" query parameter.
     *
     * @param Request $request
     */
    final protected function checkPaymentResult(Request $request): void
    {
        $this->payment = $this->goldenpay->result($request->query('payment_key'));

        $this->fireEvent();
    }

    /**
     * Fires event based on payment status.
     */
    private function fireEvent(): void
    {
        if ($this->paymentSuccessful()) {
            $this->event->execute('goldenpay.payment_events.successful', $this->payment);
            return;
        }

        $this->event->execute('goldenpay.payment_events.failed', $this->payment);
    }

    /**
     * Returns state if payment was successful.
     *
     * @return bool
     */
    protected function paymentSuccessful(): bool
    {
        return $this->payment->successful;
    }
}
