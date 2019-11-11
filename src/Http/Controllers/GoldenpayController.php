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
    protected $app;
    /**
     * @var Request
     */
    protected $request;
    /**
     * @var Goldenpay
     */
    protected $goldenpay;
    /**
     * @var Dispatcher
     */
    protected $dispatcher;
    /**
     * @var Repository
     */
    protected $config;

    /**
     * Controller constructor.
     *
     * @param Application $app
     * @param Repository $config
     * @param Request $request
     * @param Goldenpay $goldenpay
     * @param Dispatcher $dispatcher
     */
    public function __construct(
        Application $app,
        Repository $config,
        Request $request,
        Goldenpay $goldenpay,
        Dispatcher $dispatcher
    ) {
        $this->app = $app;
        $this->config = $config;
        $this->request = $request;
        $this->goldenpay = $goldenpay;
        $this->dispatcher = $dispatcher;

        $this->checkPaymentResult();
    }

    /**
     * Checks payment result with "payment_key" query parameter.
     */
    final protected function checkPaymentResult(): void
    {
        $payment = $this->goldenpay->paymentResult($this->request->query('payment_key'));

        $this->fireEvent($payment);
    }

    /**
     * Fires event based on payment status.
     *
     * @param Payment $payment
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    private function fireEvent(Payment $payment): void
    {
        if ($payment->status === 1) {
            $event = $this->app->make(
                $this->config->get('goldenpay.events.payment_successful'),
                [$payment]
            );
        } else {
            $event = $this->app->make(
                $this->config->get('goldenpay.events.payment_failed'),
                [$payment]
            );
        }

        $this->dispatcher->dispatch($event);
    }
}
