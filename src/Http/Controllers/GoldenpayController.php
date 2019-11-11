<?php

namespace Orkhanahmadov\LaravelGoldenpay\Http\Controllers;

use Illuminate\Contracts\Config\Repository;
use Orkhanahmadov\Goldenpay\Goldenpay as GoldenpayLibrary;

class GoldenpayController
{
    /**
     * @var GoldenpayLibrary
     */
    private $goldenpay;

    /**
     * GoldenpayController constructor.
     *
     * @param Repository $config
     */
    public function __construct(Repository $config)
    {
        $this->goldenpay = new GoldenpayLibrary(
            $config->get('goldenpay.auth_key'),
            $config->get('goldenpay.merchant_name')
        );
    }
}
