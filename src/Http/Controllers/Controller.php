<?php

namespace Orkhanahmadov\LaravelGoldenpay\Http\Controllers;

use Illuminate\Contracts\Events\Dispatcher;
use Orkhanahmadov\LaravelGoldenpay\Goldenpay;

abstract class Controller
{
    /**
     * @var Goldenpay
     */
    protected $goldenpay;
    /**
     * @var Dispatcher
     */
    protected $dispatcher;

    /**
     * Controller constructor.
     *
     * @param Goldenpay $goldenpay
     * @param Dispatcher $dispatcher
     */
    public function __construct(Goldenpay $goldenpay, Dispatcher $dispatcher)
    {
        $this->goldenpay = $goldenpay;
        $this->dispatcher = $dispatcher;
    }
}
