<?php

namespace App\Console\Commands;

use App\Payment;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FixPendingNoLimitExpiredToBeChargedLast extends Command
{
    /**
     * Usage example:
     *
     * php artisan xgrow:fix-pending-no-limit-expired-to-be-charged-last --log-only --initial_date=2023-04-01 --final_date=2023-04-11
     *
     */
    protected $signature = 'xgrow:fix-pending-no-limit-expired-to-be-charged-last '.
    '{--log-only : no real transaction, only logs}'.
    '{--initial_date= : Date initial} '.
    '{--final_date= : Date final} ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Commande for listing no limits payments expired and move to be the last payment date.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $logOnly = $this->option('log-only') ?? false;
        $initialDate = $this->option('initial_date') ?? null;
        $finalDate = $this->option('final_date') ?? null;

        if ( is_null($initialDate) || is_null($finalDate)) {
            echo 'Date interval is incorrect.';
            return;
        }

        Log::withContext(['command_correlation_id' => (string) Str::uuid(), 'command'=>'fix-pending-no-limit-expired-to-be-charged-last']);

        Log::info('Command fix-pending-no-limit-expired-to-be-charged-last');

        $payments = DB::select(
            'select v.codigo_compra, v.payment_date
            from view_nolimit_with_pending_charges v
            where v.payment_date between ? and ?
            order by v.payment_date',
            [$initialDate, $finalDate]
        );

        Log::info('Number of payments to update = '. count($payments) );

        foreach ($payments as $payment) {

            $lastPayment = Payment::select('payment_date')
                ->where('order_number', $payment->codigo_compra)
                ->orderBy('payment_date', 'DESC')
                ->first();

            $lastPaymentDate = Carbon::parse($lastPayment->payment_date)->addMonth(1)->format('Y-m-d');
            $paymentToUpdate = Payment::where('payment_date', $payment->payment_date)
                ->where('order_number', $payment->codigo_compra)
                ->first();

            if(!empty($paymentToUpdate)) {
                Log::info('Update payment: ID: ' . $paymentToUpdate->id ?? 0 . ' to payment_date = ' . $lastPaymentDate . ' Log Only ' . $logOnly , ['payment' => $paymentToUpdate->toArray()]);
                if ( !$logOnly ) {
                    $paymentToUpdate->payment_date = $lastPaymentDate;
                    $paymentToUpdate->save();
                }
            }

        }

        Log::info('Command fix-pending-no-limit-expired-to-be-charged-last finished');

        Log::withoutContext();

        return 0;
    }
}
