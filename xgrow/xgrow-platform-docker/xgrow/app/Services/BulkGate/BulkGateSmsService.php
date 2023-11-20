<?php

namespace App\Services\BulkGate;

use App\Services\Contracts\SendSmsInterface;
use App\Services\Contracts\RejectableInterface;
use App\Services\Objects\PhoneResponse;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class BulkGateSmsService implements SendSmsInterface, RejectableInterface
{

    private $host;
    private $promotionalSmsPath;
    private $applicationId;
    private $applicationToken;
    private $rejected = [];
    private $attemptSend = [];

    public function __construct()
    {
        $this->host = config('ads.bulkgate.api_host');
        $this->promotionalSmsPath = config('ads.bulkgate.api_sms_path');
        $this->applicationId = config('ads.bulkgate.application_id');
        $this->applicationToken = config('ads.bulkgate.application_token');
    }

    /**
     * @inheritDoc
     *
     * @param array $numbers BulkGate accepts phones with and without international code (eg: +55)
     * if international code is not supplied application default is "br"
     */
    public function sendSmsToNumbers(string $text, array $numbers): array
    {
        $this->rejected = []; // clear rejected on every subsequent call
        $this->attemptSend = $this->filterNumbers($numbers);
        $request = [
            'application_id' => $this->applicationId,
            'application_token' => $this->applicationToken,
            'number' => join(';', $this->attemptSend),
            'text' => $text,
            'country' => "br", // default: null
            //'schedule' => Carbon::now()->addMinutes(5)->timestamp,  // "2018-05-14T18:30:00-01:00" ou timestamp, default: now
        ];
        $data = $this->doRequest($request);
        Log::debug('BulkGate', ['body' => $data]);
        return $this->processResponse($data['data']['response']);
    }

    public function getRejected(): array
    {
        return $this->rejected;
    }

    private function filterNumbers(array $numbers)
    {
        return collect($numbers)->filter(function ($number) {
            $stripped =  preg_replace('/[^0-9]/', '', $number);
            $looksMobilePhone = strlen($stripped) >= 11;
            if (!$looksMobilePhone) {
                $this->rejected[] = $number;
            }
            return $looksMobilePhone;
        })->toArray();
    }

    /**
     * @param array $request
     * @throw GuzzleHttp\Exception\ClientException
     */
    private function doRequest(array $request): array
    {
        $guzzleClient = new Client(["base_uri" => $this->host]);

        $result = $guzzleClient->post($this->promotionalSmsPath, ["json" => $request]);

        $data = json_decode($result->getBody(), $return = true);

        return $data;
    }

    /**
     * @param iterable $items
     * @return \App\Services\Objects\PhoneResponse[]
     */
    private function processResponse(iterable $items): array
    {
        $results = [];
        foreach ($items as $item) {
            $results[] = $this->processSingleItem($item);
        }

        return $results;
    }

    /**
     * @param array $item
     * @return \App\Services\Objects\PhoneResponse
     */
    private function processSingleItem(array $item): PhoneResponse
    {
        $successValues = [
            'accepted' => 'Message accepted',
            'scheduled' => 'Message scheduled'
        ];

        $status = $item['status'];
        $isSuccessful = array_key_exists($status, $successValues);
        $code = $isSuccessful ? 200 : $item['code'];
        $message = $isSuccessful ? $successValues[$status] : $item['error'];
        $destination = $item['number'];
        $id = $isSuccessful ? $item['sms_id'] : null; // null on error

        return new PhoneResponse($isSuccessful, $code, $message, $destination, $id);
    }


}
