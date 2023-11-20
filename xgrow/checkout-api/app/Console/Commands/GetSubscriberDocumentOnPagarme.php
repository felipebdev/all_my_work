<?php

namespace App\Console\Commands;

use App\Payment;
use App\Services\MundipaggService;
use App\Subscriber;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use PagarMe\Client;

class GetSubscriberDocumentOnPagarme extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'xgrow:get-subscriber-document {platform_id}';

    protected Client $pagarme;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is used to get the subscriber document on pagarme and update the subscribers table.';

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
        $platform_id = $this->argument('platform_id');

        $payments = Payment::select('payments.id', 'payments.subscriber_id', 'payments.customer_id', 'subscribers.id as subscriber_id')
            ->join('subscribers', 'subscribers.id', 'payments.subscriber_id')
            ->where(['payments.platform_id' => $platform_id])
            ->where(function ($query) {
                $query->whereNull('subscribers.document_number')
                    ->orWhere('subscribers.document_number', '');
            })
            ->get();

        $mundipaggService = new MundipaggService();

        $subscribersAfected = 0;

        foreach ($payments as $payment) {

            $mundipaggCustomer = $mundipaggService->getClient()->getCustomers()->getCustomer($payment->customer_id);

            if ( isset($mundipaggCustomer->document) ) {

                Subscriber::where('id', $payment->subscriber_id)
                    ->update([
                        'document_number' => $mundipaggCustomer->document
                    ]);

                $subscribersAfected++;

            }
        }

        Log::info('xgrow:get-subscriber-document', [ 'subscribersAfected' => $subscribersAfected]);

        return Command::SUCCESS;
    }
}
