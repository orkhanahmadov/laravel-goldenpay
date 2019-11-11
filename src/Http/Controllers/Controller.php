<?php

namespace Orkhanahmadov\LaravelGoldenpay\Http\Controllers;

use Illuminate\Contracts\Config\Repository;
use Orkhanahmadov\Goldenpay\Goldenpay;

abstract class Controller
{
    /**
     * @var Goldenpay
     */
    protected $goldenpay;

    /**
     * Controller constructor.
     *
     * @param Repository $config
     */
    public function __construct(Repository $config)
    {
        $this->goldenpay = new Goldenpay(
            $config->get('goldenpay.auth_key'),
            $config->get('goldenpay.merchant_name')
        );
    }
}
