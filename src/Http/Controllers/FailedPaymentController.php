<?php

namespace Orkhanahmadov\LaravelGoldenpay\Http\Controllers;

use Illuminate\Http\Request;
use Orkhanahmadov\LaravelGoldenpay\Events\PaymentFailedEvent;

class FailedPaymentController extends Controller
{
    /**
     * @param Request $request
     */
    public function __invoke(Request $request)
    {
        $payment = $this->goldenpay->paymentResult($request->query('payment_key'));

        $this->dispatcher->dispatch(new PaymentFailedEvent($payment));
    }
}
