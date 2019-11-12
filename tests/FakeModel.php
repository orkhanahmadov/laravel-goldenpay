<?php

namespace Orkhanahmadov\LaravelGoldenpay\Tests;

use Illuminate\Database\Eloquent\Model;
use Orkhanahmadov\LaravelGoldenpay\Traits\SinglePayable;

class FakeModel extends Model
{
    use SinglePayable;

    public $timestamps = false;
}
