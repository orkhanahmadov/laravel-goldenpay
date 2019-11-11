<?php

namespace Orkhanahmadov\LaravelGoldenpay\Http\Controllers;

use Illuminate\Http\Request;
use Orkhanahmadov\LaravelGoldenpay\Goldenpay;

class PaymentResultController
{
    /**
     * @var Goldenpay
     */
    private $goldenpay;

    public function __construct(Goldenpay $goldenpay)
    {
        $this->goldenpay = $goldenpay;
    }

    public function success(Request $request)
    {
//        $this->goldenpay->paymentKey();
    }
}
