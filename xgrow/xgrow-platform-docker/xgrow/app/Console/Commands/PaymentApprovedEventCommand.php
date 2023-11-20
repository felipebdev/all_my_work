<?php

namespace App\Console\Commands;

use App\Events\Payments\PaymentApprovedEvent;
use App\Payment;
use App\Platform;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PeriodOptionException extends Exception 
{
    public function __construct() 
    {
        parent::__construct('Period option command is invalid');
    }
}

class IdOptionException extends Exception 
{
    public function __construct() 
    {
        parent::__construct('Ids option command is invalid');
    }
}

class PaymentApprovedEventCommand extends Command 
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "xgrow:events:payment-approved 
                            {platform : Platform ID} 
                            {--ids= : Comma separated payment ids (Ex:'1,2,3')} 
                            {--period= : Date period (Ex: '2021-01-01 00:00:00/2021-05-07 23:59:59')}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Launches approved payment event';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        try {
            $ids = $this->option('ids');
            $period = $this->option('period');
            $this->validateInputIds($ids);
            $this->validateInputPeriod($period);

            $platform = Platform::findOrFail($this->argument('platform'));
            $payments = Payment::
                select(
                    DB::raw(
                        "*,
                        (
                            SELECT COUNT(p.id)
                            FROM payments p
                            WHERE p.order_code = payments.order_code
                                AND payments.platform_id = '{$platform->id}'
                        ) AS transactions_count"
                    )
                )
                ->status(Payment::STATUS_PAID)
                ->platform($platform->id)
                ->when($ids, function ($q, $ids) {
                    $whereIn = explode(',', $ids);
                    $q->whereIn('payments.id', $whereIn);
                })
                ->when($period, function ($q, $period) {
                    $dates = explode('/', $period);
                    $q->whereBetween('payments.created_at', [$dates[0], $dates[1]]);
                }) 
                ->groupBy('payments.order_code') 
                ->get();
            
            Log::info('### PAYMENT APPROVED COMMAND START ###');
            Log::info('Total payments to process: '.$payments->count());

            $this->info('### PAYMENT APPROVED COMMAND START ###');
            $this->line('');

            $bar = $this->output->createProgressBar($payments->count());
            $bar->start();
                foreach ($payments as $key => $payment) {
                    try {
                        if ($payment->transactions_count > 1) {
                            $payment->plans_value = $payment->plans_value * $payment->transactions_count;
                            $payment->customer_value = $payment->customer_value * $payment->transactions_count;
                            $payment->service_value = $payment->service_value * $payment->transactions_count;
                            $payment->tax_value = $payment->tax_value * $payment->transactions_count;
                            $payment->antecipation_value = $payment->antecipation_value * $payment->transactions_count;
                        }
    
                        $subscriber = $payment->subscriber;
                        PaymentApprovedEvent::dispatch($platform, $subscriber, $payment);
                        Log::debug(($key+1).')', [
                            'payment' => [
                                'id' => $payment->id,
                                'type' => $payment->type,
                                'payment_date' => $payment->payment_date,
                                'price' => $payment->price,
                                'plans_value' => $payment->plans_value,
                                'customer_value' => $payment->customer_value,
                                'status' => $payment->status,
                                'order_code' => $payment->order_code,
                                'transactions_count' => $payment->transactions_count,
                                'created_at' => $payment->created_at
                            ],
                            'subscriber' => [
                                'id' => $subscriber->id,
                                'name' => $subscriber->email,
                                'document_number' => $subscriber->document_number
                            ],
                            'platform' => [
                                'id' => $platform->id,
                                'name' => $platform->name
                            ]
                        ]);
                    }
                    catch(Exception $exception) {
                        Log::error('X', ['payment_id', $payment->id, 'error' => $exception->getMessage()]);
                    }

                    $bar->advance();
                }
            $bar->finish();
        }
        catch(Exception $exception) {
            $this->error($exception->getMessage());
        }
    }

    private function validateInputIds($input) {
        if (empty($input)) return;

        $ids = explode(',', $input);
        foreach ($ids as $id) {
            if (!is_numeric($id)) throw new IdOptionException();
        }
    }

    private function validateInputPeriod($input) {
        if (empty($input)) return;

        $period = explode('/', $input);
        if (count($period) !== 2) throw new PeriodOptionException();
        if (!validateDate($period[0])) throw new PeriodOptionException();
        if (!validateDate($period[1])) throw new PeriodOptionException();
    }
}