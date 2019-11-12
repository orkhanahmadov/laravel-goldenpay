<?php

namespace Orkhanahmadov\LaravelGoldenpay\Tests;

use Illuminate\Database\Eloquent\Model;
use Orkhanahmadov\LaravelGoldenpay\Traits\SinglePayable;

class FakeSinglePayable extends Model
{
    use SinglePayable;

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
