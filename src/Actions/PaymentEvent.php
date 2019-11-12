<?php

namespace Orkhanahmadov\LaravelGoldenpay\Actions;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Events\Dispatcher;
use Orkhanahmadov\LaravelGoldenpay\Events\PaymentCheckedEvent;
use Orkhanahmadov\LaravelGoldenpay\Models\Payment;

class PaymentEvent
{
    /**
     * @var Repository
     */
    private $config;

    /**
     * Event constructor.
     *
     * @param Repository $config
     */
    public function __construct(Repository $config)
    {
        $this->config = $config;
    }

    /**
     * Fires event with given Payment instance.
     *
     * @param string $name
     * @param Payment $payment
     */
    public function execute(string $name, Payment $payment): void
    {
        $event = $this->config->get($name);

        $event::dispatch($payment);
    }
}
