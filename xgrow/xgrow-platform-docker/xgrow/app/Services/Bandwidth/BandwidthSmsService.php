<?php

namespace App\Services\Bandwidth;

use App\Services\Contracts\SendSmsInterface;
use App\Services\Objects\PhoneResponse;
use BandwidthLib\APIException;
use BandwidthLib\Http\ApiResponse;
use BandwidthLib\Messaging\Models\MessageRequest;
use Illuminate\Support\Facades\Log;

class BandwidthSmsService extends BandwidtBaseService implements SendSmsInterface
{

    private $consecutiveErrors = 0; // number of consecutive errors, reset to zero on success

    /**
     * @inheritDoc
     *
     * @param  array  $numbers  Bandwidth accepts phones only with international code (eg: +55)
     */
    public function sendSmsToNumbers(string $text, array $numbers): array
    {
        $this->rejected = []; // clear rejected on every subsequent call
        $attemptSend = $this->filterAndPrepareNumbers($numbers);

        $results = [];
        foreach ($attemptSend as $number) {
            $results[] = $this->sendSmsToNumber($text, $number);
        }
        return $results;
    }

    private function sendSmsToNumber($text, $number): PhoneResponse
    {
        $request = new MessageRequest();
        $request->from = $this->smsFromNumber;
        $request->to = [$number];
        $request->applicationId = $this->smsApplicationId;
        $request->text = $text;
        // $request->tag = 'tag';

        try {
            $response = $this->doRequest($request);
            return $this->handleSuccess($response, $number);
        } catch (APIException $e) {
            return $this->handleApiError($e, $text, $number);
        }
    }

    /**
     * @param  \BandwidthLib\Messaging\Models\MessageRequest  $request
     * @return \BandwidthLib\Http\ApiResponse
     * @throws \BandwidthLib\APIException
     */
    private function doRequest(MessageRequest $request): ApiResponse
    {
        $messagingClient = $this->getBandwidthClient()->getMessaging()->getClient();

        $response = $messagingClient->createMessage($this->smsAccountId, $request);

        return $response;
    }

    private function handleSuccess(ApiResponse $item, string $destination): PhoneResponse
    {
        $this->consecutiveErrors = 0;

        $result = $item->getResult();

        $code = $item->getStatusCode();
        $message = $result->text;
        $id = $result->id;

        return new PhoneResponse(true, $code, $message, $destination, $id);
    }

    private function handleApiError(APIException $e, $message, $destination): PhoneResponse
    {
        Log::debug('bandwidth.sms.error', [
            'exception_class' => get_class($e),
            'exception_message' => $e->getMessage(),
            'message' => $message,
            'destination' => $destination,
        ]);

        if ($e->getCode() == 429) {
            // Too Many Requests
            $this->consecutiveErrors++;
            usleep($this->calculateExponentialBackoffTime($this->consecutiveErrors));
            return $this->sendSmsToNumber($message, $destination);
        }

        return new PhoneResponse(false, 400, $message, $destination, null);
    }

}
