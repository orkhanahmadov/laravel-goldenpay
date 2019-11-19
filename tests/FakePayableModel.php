<?php

namespace Orkhanahmadov\LaravelGoldenpay\Tests;

use Illuminate\Database\Eloquent\Model;
use Orkhanahmadov\LaravelGoldenpay\Traits\Payable;

class FakePayableModel extends Model
{
    use Payable;

    public $timestamps = false;

    /**
     * Defines payment amount for this model's payments.
     *
     * @return int
     */
    protected function amount(): int
    {
        return $this->amount;
    }

    /**
     * Defines description for this model's payments.
     *
     * @return string
     */
    protected function description(): string
    {
        return $this->name;
    }
}
