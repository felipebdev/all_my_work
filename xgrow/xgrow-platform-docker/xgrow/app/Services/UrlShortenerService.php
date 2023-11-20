<?php

namespace App\Services;

use App\Http\Traits\CustomResponseTrait;
use Illuminate\Support\Facades\Log;

class UrlShortenerService
{
    use CustomResponseTrait;

    protected $url;
    protected $token;

    public function __construct()
    {
        $this->url = config('services.urlshortener.url');
        $this->token = config('services.urlshortener.token');
    }

    public function getShortLink($platformId, $planId, $affiliateId, $redirectUri)
    {
        $url = $this->url."?platformId=".$platformId."&planId=".$planId."&affiliateId=".$affiliateId."&redirectUri=".$redirectUri."&apiKey=".$this->token;

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_POSTFIELDS => "",
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json"
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            Log::info('Erro na geracao do link encurtado:', [$err]);
        } else {
            $response = json_decode($response, false);

            return $response->data->shortUrl ?: $redirectUri;
        }
    }
}
