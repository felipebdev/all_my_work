<?php

namespace App\Console\Commands\Fix;

use App\Payment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use PagarMe\Client;

class Sx372RedeliverPostbackForExpiredPixCommand extends Command
{
    protected $signature = 'xgrow:fix:sx372 '.
    '{--start_date= : Payment date start (Y-m-d)} '.
    '{--end_date= : Payment date end (Y-m-d)} '.
    '{--platform_id= : Restrict to single platform} '.
    '{--transaction_id= : Restrict to single transaction (optional)} '.
    '{--dry-run : Run in test mode (no real transaction)}';

    protected $description = 'Redeliver postback for expired PIX';

    public function handle()
    {
        $pagarme = new Client(env('PAGARME_API_KEY'));

        $payments = Payment::query()
            ->where('type_payment', Payment::TYPE_PAYMENT_PIX)
            ->where('status', Payment::STATUS_EXPIRED)
            ->when($this->option('start_date'), function ($query, $date) {
                $query->where('payment_date', '>', $date);
            })
            ->when($this->option('end_date'), function ($query, $date) {
                $query->where('payment_date', '<', $date);
            })
            ->when($this->option('platform_id'), function ($query, $platformId) {
                $query->where('platform_id', $platformId);
            })
            ->when($this->option('transaction_id'), function ($query, $transactionId) {
                $query->where('charge_id', $transactionId);
            })
        ->lazyById();

        Log::debug('SX-372: Processing started', [
            'total_found' => $payments->count(),
        ]);

        $redelived = 0;
        foreach ($payments as $payment) {
            Log::debug('SX-372: Processing payment', ['payment_id' => $payment->id]);

            $postbacks = $pagarme->postbacks()->getList([
                'model' => 'transaction',
                'model_id' => $payment->charge_id,
            ]);

            $lastPostback = array_last($postbacks);

            $probablyPaid = str_contains($lastPostback->payload, 'transaction%5Bstatus%5D=paid');

            Log::debug('SX-372: Redelivering postback', [
                'payment_id' => $payment->id,
                'model_id' => $lastPostback->model_id,
                'probably_paid' => $probablyPaid
            ]);

            if ($this->option('dry-run')) {
                Log::debug('SX-372: Redelivering postback skipped (dry-run)');
                sleep(2);
                continue;
            }

            $postbackRedeliver = $pagarme->postbacks()->redeliver([
                'model' => 'transaction',
                'model_id' => $payment->charge_id,
                'postback_id' => $lastPostback->id,
            ]);

            $redelived++;

            sleep(4);
        }

        Log::debug('SX-372: Processing ended', [
            'total_redelivered' => $redelived,
        ]);


        return Command::SUCCESS;
    }
}
