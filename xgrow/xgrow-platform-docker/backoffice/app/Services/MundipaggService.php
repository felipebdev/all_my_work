<?php

namespace App\Services;

use App\Constants;
use MundiAPILib\MundiAPIClient;
use App\Http\Controllers\Controller;
use App\Integration;
use Illuminate\Support\Facades\Auth;

class MundipaggService extends Controller
{
    private $client;
    private $guzzle;
    private $tokens;
    private $platformId;

    public function __construct($platform_id)
    {
        $this->setPlatformId($platform_id);

        if ($this->prepareCredentials() ) {
            $this->client = new MundiAPIClient($this->getSecretKey());
        }

    }

    public function getClient()
    {
        return $this->client;
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
            ->where('id_integration', '=', Constants::CONSTANT_INTEGRATION_MUNDIPAGG)
            ->where('flag_enable', '=', 1)
            ->first();

        if ($integration !== null ) {
            $data = json_decode($integration->source_token, false);
            $env = config('app.env');
            $this->tokens = $data->$env;
            return true;
        }
        return false;

    }

    public function getCountId()
    {
        return $this->tokens->count_id;
    }

    public function getPublicKey()
    {
        return env('MUNDIPAGG_PUBLIC_KEY', $this->tokens->public_key);
    }

    public function getSecretKey()
    {
        return env('MUNDIPAGG_SECRET_KEY', $this->tokens->secret_key);
    }


}
