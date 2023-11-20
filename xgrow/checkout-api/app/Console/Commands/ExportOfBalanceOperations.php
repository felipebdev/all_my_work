<?php

namespace App\Console\Commands;

use App\Jobs\ExportOfBalanceOperationsJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PagarMe\Client;

class ExportOfBalanceOperations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'xgrow:export-of-balance-operation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for export of balance operation spreadsheet';

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

        Log::withContext(['command_correlation_id' => (string) Str::uuid(), 'command'=>'balance-operation']);

        Log::info('Command for export of balance operation spreadsheet');

        $key = env('PAGARME_API_KEY');
        $pagarme = new Client($key);

        // define o número de registros por página
        $per_page = 1000;

        // define o número inicial da página
        $page = 1;
        $rowFile = 1;
        $reportData = [];
        // loop para buscar os registros enquanto houver dados
        while (true) {
            $balanceOperations = $pagarme->balanceOperations()->getList(['count'=>1000, 'page'=> $page, 'start_date'=> 1659322800000]);

            if (count($balanceOperations) == 0) {
                break; // interrompe o loop se não houver mais dados
            }

            foreach($balanceOperations as $cod=>$balanceOperation) {

                $reportData[$rowFile]['id'] = $balanceOperation->id;
                $reportData[$rowFile]['status'] = $balanceOperation->status;
                $reportData[$rowFile]['type'] = $balanceOperation->type;
                $reportData[$rowFile]['amount'] = $balanceOperation->amount;
                $reportData[$rowFile]['fee'] = $balanceOperation->fee;
                $reportData[$rowFile]['date_created'] = $balanceOperation->date_created;
                $reportData[$rowFile]['movement_object_id'] = $balanceOperation->movement_object->id;
                $reportData[$rowFile]['movement_object_status'] = $balanceOperation->movement_object->status;
                $reportData[$rowFile]['movement_object_amount'] = $balanceOperation->movement_object->amount;
                $reportData[$rowFile]['movement_object_fee'] = $balanceOperation->movement_object->fee;
                $reportData[$rowFile]['movement_object_installment'] = $balanceOperation->movement_object->installment ?? "";
                $reportData[$rowFile]['movement_object_antecipation_fee'] = $balanceOperation->movement_object->anticipation_fee ?? "";
                $reportData[$rowFile]['movement_object_transaction_id'] = $balanceOperation->movement_object->transaction_id ?? "";
                $reportData[$rowFile]['movement_object_recipient_id'] = $balanceOperation->movement_object->recipient_id ?? "";
                $reportData[$rowFile]['movement_object_payment_method'] = $balanceOperation->movement_object->payment_method ?? "";

                Log::info("", ['data'=> $reportData[$rowFile]]);

                $rowFile ++;

            }

            dump($page);

            $page ++;

            sleep(0.5);
        }

        Log::info('Command for export of balance operation spreadsheet finished');

        Log::withoutContext();
    }
}
