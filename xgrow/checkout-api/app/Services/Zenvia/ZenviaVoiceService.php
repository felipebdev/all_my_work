<?php

namespace App\Services\Zenvia;

use App\Services\Contracts\RejectableInterface;
use App\Services\Contracts\SendVoiceInterface;
use App\Services\Objects\PhoneResponse;
use GuzzleHttp\Client;

/**
 * Class ZenviaVoiceService
 *
 * Service for communicating with TotalVoice/Zenvia voice calling API.
 *
 * @package App\Services\Zenvia
 */
class ZenviaVoiceService implements SendVoiceInterface, RejectableInterface
{

    private $host;
    private $voicePath;
    private $token;
    private $rejected = [];
    private $attemptSend = [];

    public function __construct()
    {
        $this->host = config('ads.zenvia.api_host');
        $this->voicePath = config('ads.zenvia.api_voice_path');
        $this->token = config('ads.zenvia.application_token');
    }

    /**
     * @inheritDoc
     *
     * @param array $numbers TotalVoice/Zenvia accepts only Brazilian 11-digit phone numbers
     */
    public function sendVoiceToNumbers(string $publicAudioUrl, array $numbers): array
    {
        $this->rejected = []; // clear rejected on every subsequent call
        $this->attemptSend = $this->filterNumbers($numbers);

        $results = [];
        foreach ($this->attemptSend as $number) {
            $results[] = $this->sendVoiceToNumber($publicAudioUrl, $number);
        }
        return $results;
    }

    public function getRejected(): array
    {
        return $this->rejected;
    }

    private function filterNumbers(array $numbers)
    {
        return collect($numbers)->filter(function ($number) {
            $stripped =  preg_replace('/[^0-9]/', '', $number);
            $looksMobilePhoneBr = strlen($stripped) === 11;
            if (!$looksMobilePhoneBr) {
                $this->rejected[] = $number;
            }
            return $looksMobilePhoneBr;
        })->toArray();
    }

    /**
     * @param string $publicAudioUrl
     * @param string $number
     * @return \App\Services\Objects\PhoneResponse
     */
    private function sendVoiceToNumber(string $publicAudioUrl, string $number)
    {
        $request = [
            'numero_destino' => $number,
            'url_audio' => $publicAudioUrl,
            'resposta_usuario' => false, // default true
//            "gravar_audio" => false, // default false
//            "bina" => "0000000000", // DDD + numero
//            "detecta_caixa" => true, // default true
//            "bina_inteligente" => true, // default true
        ];

        $data = $this->doRequest($request);

        return $this->processResponse($data, $number);
    }

    /**
     * @param $request
     * @return array
     */
    private function doRequest($request): array
    {
        $guzzleClient = new Client([
            'base_uri' => $this->host,
            'headers' => [
                'Access-Token' => $this->token,
            ]
        ]);

        $result = $guzzleClient->post($this->voicePath, ["json" => $request]);

        $data = json_decode($result->getBody(), $return = true);

        return $data;
    }

    /**
     * @param array $item
     * @return \App\Services\Objects\PhoneResponse
     */
    private function processResponse(array $item, string $destination)
    {
        $isSuccessful = $item['sucesso'];

        $code = $item['status'];
        $message = $item['mensagem'];
        $id = $isSuccessful ? $item['dados']['id'] : null;

        return new PhoneResponse($isSuccessful, $code, $message, $destination, $id);
    }
}
