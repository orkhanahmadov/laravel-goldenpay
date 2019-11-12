<?php

namespace Orkhanahmadov\LaravelGoldenpay\Http\Controllers;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use Orkhanahmadov\LaravelGoldenpay\Goldenpay;
use Orkhanahmadov\LaravelGoldenpay\Http\Requests\Request;
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
     * @param Dispatcher $dispatcher
     * @param Goldenpay $goldenpay
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

        $this->fireEvent();
    }

    /**
     * Fires event based on payment status.
     */
    private function fireEvent(): void
    {
        if ($this->successful() === 1) {
            $event = $this->application->make(
                $this->config->get('goldenpay.events.payment_successful'),
                [$this->payment]
            );
        } else {
            $event = $this->application->make(
                $this->config->get('goldenpay.events.payment_failed'),
                [$this->payment]
            );
        }

        $this->dispatcher->dispatch($event);
    }

    /**
     * Returns state if payment was successful.
     *
     * @return bool
     */
    protected function successful(): bool
    {
        return $this->payment->status === 1;
    }
}
