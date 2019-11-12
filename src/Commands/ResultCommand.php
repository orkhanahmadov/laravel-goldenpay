<?php

namespace Orkhanahmadov\LaravelGoldenpay\Commands;

use Illuminate\Console\Command;
use Orkhanahmadov\LaravelGoldenpay\Goldenpay;
use Orkhanahmadov\LaravelGoldenpay\Models\Payment;

class ResultCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'goldenpay:result {paymentKey?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks payment result for given or all pending payments';

    /**
     * Execute the console command.
     *
     * @param Goldenpay $goldenpay
     */
    public function handle(Goldenpay $goldenpay): void
    {
        $payment = Payment::wherePaymentKey($this->argument('paymentKey'))->firstOrFail();

        $goldenpay->result($payment);
    }
}
