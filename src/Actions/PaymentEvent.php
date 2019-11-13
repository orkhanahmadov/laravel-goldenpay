<?php

namespace Orkhanahmadov\LaravelGoldenpay\Actions;

use Illuminate\Contracts\Config\Repository;
use Orkhanahmadov\LaravelGoldenpay\Models\Payment;

class PaymentEvent
{
    /**
     * @var Repository
     */
    private $config;
    /**
     * @var bool
     */
    private $enabled = true;

    /**
     * Event constructor.
     *
     * @param Repository $config
     */
    public function __construct(Repository $config)
    {
        $this->config = $config;
        $this->enabled = $config->get('goldenpay.payment_events.enabled', true);
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

        if ($event && $this->enabled) {
            $event::dispatch($payment);
        }
    }
}
