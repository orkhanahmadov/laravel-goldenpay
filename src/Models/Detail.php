<?php

namespace Orkhanahmadov\LaravelGoldenpay\Models;

use Illuminate\Database\Eloquent\Model;

class Detail extends Model
{
    protected $dates = [
        'resulted_at',
    ];

    protected $hidden = [
        'card_number',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('goldenpay.tables.payment_details'));
    }
}
