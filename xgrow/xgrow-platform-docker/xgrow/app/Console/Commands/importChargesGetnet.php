<?php

namespace App\Console\Commands;

use DB;
use App\Platform;
use App\Services\GetnetService;
use Illuminate\Console\Command;

class importChargesGetnet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:import-charges';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importa cobranças do Getnet';

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
     * @return mixed
     */
    public function handle()
    {
        $platforms = Platform::join('integrations', 'integrations.platform_id', '=', 'platforms.id')
            ->where('platforms.deleted_at', null)
            ->where('integrations.id_webhook', 4)
            ->select('platforms.id AS platform_id', 'integrations.id_webhook')
            ->get();

        if ($platforms->count() > 0 ) {

            foreach($platforms as $platform) {

                $params = ['page' => 1, 'limit' => 500];
                $callback = $this->importCharges($platform->platform_id, $params);

                if(isset($callback->charges) && count($callback->charges) > 0) {
                    $this->recordCharges($callback->charges);
                }

                $calls = (int) ceil($callback->total / 500);

                if($calls > 1) {
                    for ($i = 2; $i <= $calls; $i++) {
                        $params = ['page' => $i, 'limit' => 500];
                        $callback = $this->importCharges($platform->platform_id, $params);

                        if(isset($callback->charges) && count($callback->charges) > 0) {
                            $this->recordCharges($callback->charges);
                        }
                    }
                }
            }
        }
    }

    public function importCharges($platformId, $params)
    {
        $getnetService = new GetnetService($platformId);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $getnetService->getUrlApi() . "/v1/charges?page=" . $params['page'] . "&limit=" . $params['limit'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/x-www-form-urlencoded",
                "Authorization: Bearer " . $getnetService->getToken(),
                "seller_id: " . $getnetService->getSellerId()
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response, false);
    }

    public function recordCharges($charges)
    {
        foreach($charges as $charge){

            $data["amount"] = substr($charge->amount, 0, -2) . '.' . substr($charge->amount, -2) ;
            $data["charge_id"] = $charge->charge_id;
            $data["seller_id"] = $charge->seller_id;
            $data["subscription_id"] = $charge->subscription_id;
            $data["customer_id"] = $charge->customer_id;
            $data["plan_id"] = $charge->plan_id;
            $data["payment_id"] = $charge->payment_id;
            $data["status"] = $charge->status;
            $data["scheduled_date"] = $charge->scheduled_date;
            $data["create_date"] = $charge->create_date;
            $data["retry_number"] = $charge->retry_number;
            $data["payment_date"] = $charge->payment_date;
            $data["payment_type"] = $charge->payment_type;
            $data["terminal_nsu"] = $charge->terminal_nsu;
            $data["authorization_code"] = $charge->authorization_code;
            $data["acquirer_transaction_id"] = $charge->acquirer_transaction_id;
            $data["installment"] = $charge->installment;

            $insert = DB::table('getnet_charges')->insertOrIgnore($data); // funciona
            if ($insert === 0){
                $update = DB::table('getnet_charges')->where('charge_id', $data['charge_id']);
                $update->update($data);
            }
        }

    }
}
