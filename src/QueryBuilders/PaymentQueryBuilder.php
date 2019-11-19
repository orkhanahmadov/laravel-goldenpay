<?php

namespace Orkhanahmadov\LaravelGoldenpay\QueryBuilders;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Orkhanahmadov\LaravelGoldenpay\Models\Payment;

class PaymentQueryBuilder extends Builder
{
    /**
     * "whereSuccessful()" scope to filter only successful payments.
     * Successful payments are payments with "status" field value equal to STATUS_SUCCESSFUL constant value.
     *
     * @return PaymentQueryBuilder
     */
    public function whereSuccessful(): PaymentQueryBuilder
    {
        return $this->where('status', Payment::STATUS_SUCCESSFUL);
    }

    /**
     * "wherePending()" scope to filter only pending payments.
     * Pending payments are:
     * "status" field anything other than STATUS_SUCCESSFUL constant value,
     * PLUS
     * "checks" field less than MINIMUM_REQUIRED_CHECKS constant value OR "created_at" timestamp less than 30 minutes.
     *
     * @return PaymentQueryBuilder
     */
    public function wherePending(): PaymentQueryBuilder
    {
        return $this
            ->where(function (Builder $query) {
                $query->whereNull('status')
                    ->orWhere('status', '<>', Payment::STATUS_SUCCESSFUL);
            })
            ->where(function (Builder $query) {
                $query->where('checks', '<', Payment::MINIMUM_REQUIRED_CHECKS)
                    ->orWhere('created_at', '>=', Carbon::now()->subMinutes(30));
            });
    }
}
