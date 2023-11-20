<?php

namespace App\Console\Commands;

use App\Jobs\MundipaggRecurrenceOrder;
use App\Payment;
use App\PaymentPlanSplit;
use App\Recurrence;
use App\Subscriber;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Queue\QueueManager;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Usage example:
 *
 * php artisan xgrow:fix-nolimit-payment-split --log-only 64d0106d-80a2-4063-bdfb-d76205d53d67 --create_at_initial=2022-11-18 --create_at_final=2022-11-18 --order_number=6377929F450EC
 *
 */

class FixNoLimitPaymentSplit extends Command
{
    protected $signature = 'xgrow:fix-nolimit-payment-split {platform_id} '.
    '{--create_at_initial= : Date initial} '.
    '{--create_at_final= : Date final} '.
    '{--order_number= : Restrict to single order_number} '.
    '{--log-only : no real transaction, only logs} ';

    protected $description = 'Fix Nolimit Payment Split';

    public function handle(QueueManager $queueManager)
    {
        $platformId = $this->argument('platform_id') ?? null;
        $orderNumber = $this->option('order_number') ?? null;
        $createdAtInitial = $this->option('create_at_initial') ?? null;
        $createdAtFinal = $this->option('create_at_final') ?? null;
        $logOnly = $this->option('log-only') ?? false;

        $params = [
            'platform' => $platformId,
            'order_number' => $orderNumber,
            'create_at_initial' => $createdAtInitial,
            'create_at_final' => $createdAtFinal,
            'log-only' => $logOnly
        ];

        Log::withContext(['command_correlation_id' => (string) Str::uuid(), 'params' => $params]);

        Log::info('Fix Nolimit Payment Split command');

        Log::info('Fix Nolimit Payment Split command starting');

        //get all payments nolimit with paramters
        $payments = Payment::select(
            'payments.id',
            'payments.order_number'
        )
            ->where('payments.type', 'U')
            ->where('payments.installment_number', 1)
            ->whereRaw(
                "(payments.created_at >= ? AND payments.created_at <= ?)",
                [
                    $createdAtInitial ." 00:00:00",
                    $createdAtFinal ." 23:59:59"
                ]
            )
            ->when($platformId, function ($query, $platformId) {
                $query->where('payments.platform_id', $platformId);
            })
            ->when($orderNumber, function ($query, $orderNumber) {
                $query->where('payments.order_number', $orderNumber);
            });

        $sql = $payments->toSql();
        $payments = $payments->get();

        if ($payments->count() == 0) {
            Log::info('Neunhum pagamento encontrado!');
            Log::info('Fix Nolimit Payment Split SQL: ', ['sql' => $sql]);
            return;
        }

        foreach ($payments as $payment) {
            $paymentsSplit = $this->getFirstSplit($payment->id);
            $installmentsToFix = $this->getInstallmentsToFix($payment->order_number);
            $fixed = 0;
            foreach ($installmentsToFix as $installment) {

                foreach ($paymentsSplit as $split) {
                    $split['payment_plan_id'] = $installment->payment_plan_id;
                    if ( !$logOnly ) {
                        PaymentPlanSplit::create(
                            $split
                        );
                    }
                }
                $fixed ++;
            }
            Log::info('Payments fixed: ', ['payments' => $installmentsToFix]);
        }

        Log::info('Payments fixed: '.$fixed);
        Log::info('Fix Nolimit Payment Split SQL: ', ['sql' => $sql]);
        Log::info('Fix Nolimit Payment Split command finished');

        Log::withoutContext();
    }

    public function getFirstSplit($paymentId)
    {
        return Payment::select(
            'payment_plan_split.client_id',
            'payment_plan_split.platform_id',
            'payment_plan_split.product_id',
            'payment_plan_split.order_code',
            'payment_plan_split.plan_id',
            'payment_plan_split.percent',
            'payment_plan_split.value',
            'payment_plan_split.anticipation_value',
            'payment_plan_split.type',
            'payment_plan_split.producer_product_id'
        )
            ->join('payment_plan', 'payments.id', 'payment_plan.payment_id')
            ->join('payment_plan_split', 'payment_plan_split.payment_plan_id', 'payment_plan.id')
            ->where('payments.type', 'U')
            ->where('payments.installment_number', 1)
            ->where('payments.id', $paymentId)
            ->get()
            ->toArray();
    }

    public function getInstallmentsToFix($orderNumber)
    {
        //tem que ter parcela sem split
        return Payment::select(
            'payments.id',
            'payment_plan.id as payment_plan_id'
        )
            ->join('payment_plan', 'payments.id', 'payment_plan.payment_id')
            ->leftjoin('payment_plan_split', 'payment_plan_split.payment_plan_id', 'payment_plan.id')
            ->where('payments.type', 'U')
            ->where('payments.installment_number', '<>' , 1)
            ->whereNull('payment_plan_split.payment_plan_id')
            ->where('payments.order_number', $orderNumber)
            ->get();
    }
}
