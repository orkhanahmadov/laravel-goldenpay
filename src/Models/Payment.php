<?php

namespace Orkhanahmadov\LaravelGoldenpay\Models;

use Illuminate\Database\Eloquent\Model;
use Orkhanahmadov\Goldenpay\Enums\Language;

/**
 * Class Payment
 * @package Orkhanahmadov\LaravelGoldenpay\Models
 *
 * @property-read int $id
 * @property-read string $payment_key
 * @property-read int $amount
 * @property-read string $card_type
 * @property-read string $language
 * @property-read string $description
 * @property-read int $status
 * @property-read int $checks
 * @property-read \Carbon\Carbon $created_at
 * @property-read \Carbon\Carbon $updated_at
 * @method static Payment first()
 */
class Payment extends Model
{
    protected $guarded = [];

    protected $dates = [
        'payment_date',
    ];

    protected $hidden = [
        'card_number',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('goldenpay.table_name'));
    }
}
