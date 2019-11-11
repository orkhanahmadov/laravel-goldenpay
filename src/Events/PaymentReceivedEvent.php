<?php

namespace Orkhanahmadov\LaravelGoldenpay\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Orkhanahmadov\LaravelGoldenpay\Models\Payment;

class PaymentReceivedEvent
{
    use Dispatchable;
    use InteractsWithSockets;
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
