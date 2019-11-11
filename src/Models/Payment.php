<?php

namespace Orkhanahmadov\LaravelGoldenpay\Models;

use Illuminate\Database\Eloquent\Model;
use Orkhanahmadov\Goldenpay\Enums\Language;

/**
 * Class Payment
 * @package Orkhanahmadov\LaravelGoldenpay\Models
 *
 * @property int $id
 * @property string $payment_key
 * @property int $amount
 * @property string $card_type
 * @property string $language
 * @property string $description
 * @property int $status
 * @property string $message
 * @property string $reference_number
 * @property string $card_number
 * @property \Carbon\Carbon $payment_date
 * @property int $checks
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
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
