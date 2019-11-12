<?php

namespace Orkhanahmadov\LaravelGoldenpay\Tests;

use Illuminate\Database\Eloquent\Model;
use Orkhanahmadov\LaravelGoldenpay\Traits\Payable;

class FakePayableModel extends Model
{
    use Payable;

    public $timestamps = false;

    /**
     * Define description for this model's payments.
     *
     * @return string
     */
    public function description(): string
    {
        return $this->name;
    }
}
