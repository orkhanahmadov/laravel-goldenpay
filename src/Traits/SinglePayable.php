<?php

namespace Orkhanahmadov\LaravelGoldenpay\Traits;

use Illuminate\Database\Eloquent\Relations\MorphOne;
use Orkhanahmadov\LaravelGoldenpay\Models\Payment;

trait SinglePayable
{
    public function payment(): MorphOne
    {
        return $this->morphOne(Payment::class, 'payable');
    }

    /**
     * Define description for this model's payments.
     *
     * @return string
     */
//    abstract public function description(): string;
}
