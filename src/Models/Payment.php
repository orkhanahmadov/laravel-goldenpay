<?php

namespace Orkhanahmadov\LaravelGoldenpay\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Orkhanahmadov\Goldenpay\Response\PaymentKey;

/**
 * Class Payment.
 *
 * @property int $id
 * @property string $payable_type
 * @property int $payable_id
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
 * @property-read string $payment_url
 * @property-read float|int $formatted_amount
 * @property-read bool $successful
 * @method static Payment first()
 * @method static Builder successful()
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

    public const STATUS_SUCCESSFUL = 1;

    public const MINIMUM_REQUIRED_CHECKS = 5;

    /**
     * Payment constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('goldenpay.table_name'));
    }

    /**
     * Returns payment's related model.
     *
     * @return MorphTo
     */
    public function payable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * "payment_url" accessor.
     * Used to get "Goldenpay payment page url" from Payment model instance.
     * Returns "null" if payment is considered successful.
     *
     * @return string|null
     */
    public function getPaymentUrlAttribute(): ?string
    {
        if ($this->successful) {
            return null;
        }

        return (new PaymentKey($this->payment_key))->paymentUrl();
    }

    /**
     * "successful" accessor.
     * Used on Payment model instances to get if payment considered successful.
     *
     * @return bool
     */
    public function getSuccessfulAttribute(): bool
    {
        return $this->status === self::STATUS_SUCCESSFUL;
    }

    /**
     * "formatted_amount" accessor.
     * Because all amount related values stored as integer,
     * this accessor to return values as decimal.
     *
     * @return float|int
     */
    public function getFormattedAmountAttribute()
    {
        return $this->amount / 100;
    }

    /**
     * "successful()" scope to filter only successful payments.
     * Successful payments are payments with "status" field value equal to STATUS_SUCCESSFUL constant value.
     *
     * @param Builder $builder
     * @return Builder
     */
    public function scopeSuccessful(Builder $builder): Builder
    {
        return $builder->whereStatus(self::STATUS_SUCCESSFUL);
    }

    /**
     * "pending()" scope to filter only pending payments.
     * Pending payments are:
     * "status" field anything other than STATUS_SUCCESSFUL constant value,
     * PLUS
     * "checks" field less than MINIMUM_REQUIRED_CHECKS constant value OR "created_at" timestamp less than 30 minutes.
     *
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopePending(Builder $builder): Builder
    {
        return $builder
            ->where(function (Builder $query) {
                $query->whereNull('status')
                    ->orWhere('status', '<>', self::STATUS_SUCCESSFUL);
            })
            ->where(function (Builder $query) {
                $query->where('checks', '<', self::MINIMUM_REQUIRED_CHECKS)
                    ->orWhere('created_at', '>=', now()->subMinutes(30));
            });
    }
}
