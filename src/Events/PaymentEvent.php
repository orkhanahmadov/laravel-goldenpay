<?php

namespace Orkhanahmadov\LaravelGoldenpay\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Orkhanahmadov\LaravelGoldenpay\Models\Payment;

abstract class PaymentEvent
{
    use Dispatchable;
    use SerializesModels;

    /**
     * @var Payment
     */
    public $payment;

    /**
     * Create a new event instance.
     *
     * @param Payment $payment
     */
    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }
}
