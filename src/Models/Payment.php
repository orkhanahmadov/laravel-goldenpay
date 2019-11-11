<?php

namespace Orkhanahmadov\LaravelGoldenpay\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('goldenpay.tables.payments'));
    }
}
