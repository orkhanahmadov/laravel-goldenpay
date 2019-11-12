<?php

namespace Orkhanahmadov\LaravelGoldenpay\Http\Controllers;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Orkhanahmadov\LaravelGoldenpay\Goldenpay;
use Orkhanahmadov\LaravelGoldenpay\Models\Payment;

abstract class GoldenpayController
{
    /**
     * @var Application
     */
    protected $application;
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
     * @param Application $application
     * @param Repository $config
     * @param Request $request
     * @param Goldenpay $goldenpay
     * @param Dispatcher $dispatcher
     */
    public function __construct(
        Application $application,
        Repository $config,
        Request $request,
        Dispatcher $dispatcher,
        Goldenpay $goldenpay
    ) {
        $this->application = $application;
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
        $this->payment = $this->goldenpay->paymentResult($request->query('payment_key'));

        $this->fireEvent($this->payment);
    }

    /**
     * Fires event based on payment status.
     *
     * @param Payment $payment
     */
    private function fireEvent(Payment $payment): void
    {
        if ($payment->status === 1) {
            $event = $this->application->make(
                $this->config->get('goldenpay.events.payment_successful'),
                [$payment]
            );
        } else {
            $event = $this->application->make(
                $this->config->get('goldenpay.events.payment_failed'),
                [$payment]
            );
        }

        $this->dispatcher->dispatch($event);
    }
}
