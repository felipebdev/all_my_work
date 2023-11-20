<?php

namespace App\Services\Getnet;

use App\Constants;
use App\Integration;
use App\Safe;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use App\Services\GetnetService;

class SafeService extends Controller
{
    private $api;
    private $headerApi;

    public function __construct($platform_id)
    {
        $getnetApi = new GetnetService($platform_id);
        $this->api = $getnetApi->getApi();
        $this->headerApi = $getnetApi->getHeaders();
    }

    public function index()
    {

    }

    public function store($dataForm, $numberToken, $customerId)
    {
        $expiration = explode("/", $dataForm['expiration']);

        $platform_id = $dataForm['platform_id'];

        unset($dataForm['expiration']);
        unset($dataForm['platform_id']);

        $dataForm["number_token"] = $numberToken;
        $dataForm["expiration_month"] = $expiration[0];
        $dataForm["expiration_year"] = $expiration[1];
        $dataForm["customer_id"] = $customerId;
        $dataForm["verify_card"] = false;

        try {
            $this->headerApi["Content-Type"] = "application/json";

            $response = $this->api->request('POST', '/v1/cards', [
                "headers" => $this->headerApi,
                "json" => $dataForm
            ]);

            $return = json_decode($response->getBody(), false);

            $safe = Safe::where([
                'card_id' => $return->card_id,
                'platform_id' => $platform_id
            ])->firstOrNew([
                'platform_id' => $platform_id
            ]);

            $safe->number_token = $return->number_token;
            $safe->card_id = $return->card_id;
            $safe->subscriber_id = $customerId;

            $safe->save();

            $safe->integration()->delete();
            $integration = Integration::where('platform_id', $safe->platform_id)->where('id_integration', Constants::CONSTANT_INTEGRATION_GETNET)->first();
            $safe->integration()->create(['integration_id' => $integration->id, 'integration_type_id' => $safe->card_id]);

            return ['status' => 'success', 'data' => $return, 'message' => 'Assinatura concluída com sucesso!'];

        }
        catch( \Exception $e){
            $response = json_decode($e->getResponse()->getBody()->getContents(), false);
            $errors = '';

            if ((int) $response->status_code !== 200) {
                if (count($response->details)  > 0) {
                    foreach ($response->details as $item) {
                        $errors .= "{$item->description} [{$item->description_detail}] \n";
                    }
                }
            }

            return ['status' => 'error', 'data' => $response, 'message' => $errors, 'message_request' => $e->getMessage()];
        }

    }

}
