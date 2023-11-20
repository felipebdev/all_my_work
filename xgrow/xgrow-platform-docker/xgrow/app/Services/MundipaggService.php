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
        $this->client = new MundiAPIClient($this->getSecretKey());
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

    public function getPublicKey()
    {
        return env('MUNDIPAGG_PUBLIC_KEY');
    }

    public function getSecretKey()
    {
        return env('MUNDIPAGG_SECRET_KEY');
    }


}
