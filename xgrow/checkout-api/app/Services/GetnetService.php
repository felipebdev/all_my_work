<?php

namespace App\Services;

use App\Http\Controllers\Controller;
use App\Integration;
use GuzzleHttp\Client as Guzzle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class GetnetService extends Controller
{
    private $token;
    private $guzzle;
    private $tokensGetnet;
    private $platformId;

    public function __construct($platform_id)
    {
        $this->setPlatformId($platform_id);

        if ($this->prepareCredentials() ) {

            $this->guzzle = new Guzzle(['base_uri' => $this->getUrlApi()]);

            try {

                $authString = base64_encode($this->getClientId().":".$this->getSecretId());

                $response = $this->guzzle->request('POST', '/auth/oauth/v2/token', [
                    'headers' => [
                        'Content-Type' => 'application/x-www-form-urlencoded',
                        'Authorization' => 'Basic '.$authString
                    ],
                    'form_params' => [
                        'scope' => 'oob',
                        'grant_type' => 'client_credentials'
                    ]
                ]);
                $this->token = json_decode($response->getBody())->access_token;
            }
            catch( \Exception $e){
                $response = json_decode($e->getResponse()->getBody()->getContents(), false);
                return ['status' => 'error', 'data' => $response, 'message_request' => $e->getMessage()];
            }
        }

    }

    public function getToken()
    {
        return $this->token;
    }

    public function getApi()
    {
        return $this->guzzle;
    }

    public function getHeaders()
    {
        return  [
            "Authorization" => "Bearer {$this->token}",
            "seller_id" => $this->getSellerId()
        ];
    }

    public function getUser()
    {
        return Auth::user();
    }

    public function setPlatformId($value)
    {
        $this->platformId = $value;
    }

    public function getPlatformId()
    {
        return $this->platformId;
    }

    private function prepareCredentials()
    {
        $integration = Integration::where('platform_id', '=', $this->getPlatformId())
            ->where('id_integration', '=', 'GETNET')
            ->where('flag_enable', '=', 1)
            ->first();

        if ($integration !== null ) {
            $data = json_decode($integration->source_token, false);
            $env = config('app.env');
            $this->tokensGetnet = $data->$env;
            return true;
        }
        return false;

    }

    public function getSellerId()
    {
        return $this->tokensGetnet->seller_id;
    }

    public function getClientId()
    {
        return $this->tokensGetnet->client_id;
    }

    public function getSecretId()
    {
        return $this->tokensGetnet->secret_id;
    }

    public function getUrlApi()
    {
        return $this->tokensGetnet->url_api;
    }

    public function getUrlCheckout()
    {
        return $this->tokensGetnet->url_checkout;
    }
}
