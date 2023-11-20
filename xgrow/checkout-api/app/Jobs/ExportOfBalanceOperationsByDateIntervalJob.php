<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use PagarMe\Client;

class ExportOfBalanceOperationsByDateIntervalJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $startDate;
    public $endDate;
    public $context;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($startDate, $endDate, $context)
    {
        $this->onQueue('xgrow-jobs:balance-transactions');
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->context = $context;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $key = env('PAGARME_API_KEY');
        $pagarme = new Client($key);

        // define o número de registros por página
        $per_page = 1000;

        // define o número inicial da página
        $page = 1;

        Log::withContext($this->context);

        Log::info('Command for export of balance ');
        // loop para buscar os registros enquanto houver dados
        while (true) {
            $balanceOperations = $pagarme->balanceOperations()->getList(['count'=>1000, 'page'=> $page, 'start_date'=> strtotime($this->startDate).'000', 'end_date'=>strtotime($this->endDate).'999']);

            if (count($balanceOperations) == 0) {
                break; // interrompe o loop se não houver mais dados
            }

            foreach($balanceOperations as $cod=>$balanceOperation) {
                $reportData = [];
                $reportData['id'] = $balanceOperation->id;
                $reportData['status'] = $balanceOperation->status;
                $reportData['type'] = $balanceOperation->type;
                $reportData['amount'] = $balanceOperation->amount;
                $reportData['fee'] = $balanceOperation->fee;
                $reportData['date_created'] = $balanceOperation->date_created;
                $reportData['movement_object_id'] = $balanceOperation->movement_object->id;
                $reportData['movement_object_status'] = $balanceOperation->movement_object->status;
                $reportData['movement_object_amount'] = $balanceOperation->movement_object->amount;
                $reportData['movement_object_fee'] = $balanceOperation->movement_object->fee;
                $reportData['movement_object_installment'] = $balanceOperation->movement_object->installment ?? "";
                $reportData['movement_object_antecipation_fee'] = $balanceOperation->movement_object->anticipation_fee ?? "";
                $reportData['movement_object_transaction_id'] = $balanceOperation->movement_object->transaction_id ?? "";
                $reportData['movement_object_recipient_id'] = $balanceOperation->movement_object->recipient_id ?? "";
                $reportData['movement_object_payment_method'] = $balanceOperation->movement_object->payment_method ?? "";

                Log::info("", ['data'=> $reportData]);

            }

            $page ++;

            sleep(0.5);
        }

        Log::withoutContext();
    }
}
