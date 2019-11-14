<?php

namespace Orkhanahmadov\LaravelGoldenpay\Http\Controllers;

use Illuminate\Http\Request;
use Orkhanahmadov\LaravelGoldenpay\Goldenpay;
use Orkhanahmadov\LaravelGoldenpay\Models\Payment;

abstract class GoldenpayController
{
    /**
     * @var Request
     */
    protected $request;
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
     * @param Goldenpay $goldenpay
     */
    public function __construct(
        Request $request,
        Goldenpay $goldenpay
    ) {
        $this->request = $request;
        $this->goldenpay = $goldenpay;

        $this->paymentResult();
    }

    /**
     * Checks payment result with "payment_key" query parameter.
     */
    final protected function paymentResult(): void
    {
        $this->payment = $this->goldenpay->result($this->validate());
    }

    /**
     * Validates request for existence of "payment_key".
     *
     * @return string
     */
    private function validate(): string
    {
        $validated = $this->request->validate([
            'payment_key' => 'required|string',
        ]);

        return $validated['payment_key'];
    }
}
