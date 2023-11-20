<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PagarMe\Client;

class RefundByIDTransactionCSV extends Command
{
    /**
     * Usage example:
     *
     * php artisan xgrow:refund-by-id-transaction-csv --log-only
     *
     */

    protected $signature = 'xgrow:refund-by-id-transaction-csv {--log-only : no real transaction, only logs}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To refund transaction by ID';

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

        Log::withContext(['command_correlation_id' => (string) Str::uuid()]);

        Log::info('Refund by transaction ID is started');
        //read csv file
        $filePath = storage_path('app/transactions.csv');
        $file = fopen($filePath, 'r');

        $header = fgetcsv($file);

        $transactions = [];
        while ($row = fgetcsv($file)) {
            $transactions[] = array_combine($header, $row);
        }
        fclose($file);

        //for each line, get the id column
        Log::info('');

        $key = env('PAGARME_API_KEY');
        $pagarme = new Client($key);

        $n = 0;
        foreach ($transactions as $transaction) {
            //by id column send to pagarme to refund

            Log::info('Refund by transaction id: '.$transaction['ID']);

            if ( !$logOnly ) {
                try {
                    $refundedTransaction = $pagarme->transactions()->refund([
                        'id' => $transaction['ID']
                    ]);

                    Log::info('Refund by transaction id: '.$transaction['ID'], ['response' => [$refundedTransaction]]);
                } catch (Exception $e) {
                    Log::info('Refund by transaction id failed: '.$transaction['ID'], ['response' => [$e->getMessage()]]);
                }
            }

            $n++;
        }

        //log the transaction
        Log::info('Refund by transaction ID is finished with refunded transaction: '.$n);

        Log::withoutContext();
    }
}
