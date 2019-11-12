<?php

namespace Orkhanahmadov\LaravelGoldenpay\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

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
 * @property-read float|int $formatted_amount
 * @property-read bool $successful
 * @method static Payment first()
 * @method static Builder pending()
 */
class Payment extends Model
{
    protected $guarded = [
        'status',
    ];

    protected $dates = [
        'payment_date',
    ];

    protected $hidden = [
        'card_number',
    ];

    protected $casts = [
        'amount' => 'int',
        'status' => 'int',
        'checks' => 'int',
    ];

    public const PAYMENT_STATUS_CODE_SUCCESSFUL = 1;
    public const PAYMENT_MINIMUM_CHECK_COUNT_UNLESS_SUCCESSFUL = 5;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('goldenpay.table_name'));
    }

    public function getSuccessfulAttribute(): bool
    {
        return $this->status === self::PAYMENT_STATUS_CODE_SUCCESSFUL;
    }

    public function getFormattedAmountAttribute()
    {
        return $this->amount / 100;
    }

    public function scopePending(Builder $builder): Builder
    {
        return $builder
            ->where(function (Builder $query) {
                $query->whereNull('status')
                    ->orWhere('status', '<>', self::PAYMENT_STATUS_CODE_SUCCESSFUL);
            })
            ->where(function (Builder $query) {
                $query->where('checks', '<', self::PAYMENT_MINIMUM_CHECK_COUNT_UNLESS_SUCCESSFUL)
                    ->orWhere('created_at', '>', now()->subMinutes(30));
            });

//        return $builder
//            ->where(function (Builder $query) {
//                $query->whereNull('status')
//                    ->orWhere('status', '<>', self::PAYMENT_STATUS_CODE_SUCCESSFUL);
//            })
//            ->where(function (Builder $query) {
//                $query->where('checks', '<', self::PAYMENT_MINIMUM_CHECK_COUNT_UNLESS_SUCCESSFUL);
//            })
//            ->orWhere(function (Builder $query) {
//                $query->where('checks', '>=', self::PAYMENT_MINIMUM_CHECK_COUNT_UNLESS_SUCCESSFUL)
//                    ->orWhere('created_at', '>', now()->subMinutes(30));
//            });
    }
}
