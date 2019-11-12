<?php

namespace Orkhanahmadov\LaravelGoldenpay\Http\Controllers;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use Orkhanahmadov\LaravelGoldenpay\Events\PaymentSuccessfulEvent;
use Orkhanahmadov\LaravelGoldenpay\Goldenpay;
use Orkhanahmadov\LaravelGoldenpay\Http\Requests\Request;
use Orkhanahmadov\LaravelGoldenpay\Models\Payment;

abstract class GoldenpayController
{
    /**
     * @var Repository
     */
    protected $config;
    /**
     * @var Dispatcher
     */
    protected $dispatcher;
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
     * @param Repository $config
     * @param Request $request
     * @param Dispatcher $dispatcher
     * @param Goldenpay $goldenpay
     */
    public function __construct(
        Repository $config,
        Request $request,
        Dispatcher $dispatcher,
        Goldenpay $goldenpay
    ) {
        $this->config = $config;
        $this->dispatcher = $dispatcher;
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
            $event = $this->config->get('goldenpay.events.payment_successful');
        } else {
            $event = $this->config->get('goldenpay.events.payment_failed');
        }

        $this->dispatcher->dispatch(new $event($this->payment));
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
