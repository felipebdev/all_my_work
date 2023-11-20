<?php

namespace App\Services\Bandwidth;

use App\Jobs\BandwidthCallRetryJob;
use App\Repositories\Campaign\BandwidthVoiceCacheRepository;
use App\Services\Contracts\SendVoiceInterface;
use App\Services\Objects\PhoneResponse;
use BandwidthLib\APIException;
use BandwidthLib\Http\ApiResponse;
use BandwidthLib\Voice\Bxml\Hangup;
use BandwidthLib\Voice\Bxml\PlayAudio;
use BandwidthLib\Voice\Bxml\Response;
use BandwidthLib\Voice\Models\ApiCreateCallRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use UnexpectedValueException;

/**
 * Class BandwidthVoiceService
 *
 * Bandwidth needs a callback with Bandwidth XML (BXML).
 *
 * It uses a "Group UUID" and individual "Call UUID", saving on Redis and sent using "tag".
 * On callback, given UUIDs are used to retrieve and return URL in BXML.
 *
 * @package App\Services\Bandwidth
 */
class BandwidthVoiceService extends BandwidtBaseService implements SendVoiceInterface
{

    private $consecutiveErrors = 0; // number of consecutive errors, reset to zero on success
    private $retryDelay = 60 * 15; // seconds to wait before retry call
    private $maxCallAttempts = 3; // max number of call attempts
    private $callbackUrl;
    private $cache;

    public function __construct(BandwidthVoiceCacheRepository $cache)
    {
        parent::__construct();
        $this->cache = $cache;
        $this->callbackUrl = URL::route('bandwidth.voice-status-callback');
    }

    /**
     * @inheritDoc
     *
     * @param  array  $numbers  Bandwidth accepts phones only with international code (eg: +55)
     */
    public function sendVoiceToNumbers(string $url, array $numbers): array
    {
        $this->rejected = []; // clear rejected on every subsequent call
        $attemptSend = $this->filterAndPrepareNumbers($numbers);

        $groupUuid = Str::uuid(); // generate UUID for this group request

        $results = [];
        foreach ($attemptSend as $number) {
            $tag = $this->generateTag($groupUuid);
            $this->cache->storeCallInfo($tag, $url, $number);
            $results[] = $this->sendVoiceToNumber($tag, $url, $number);
        }

        BandwidthCallRetryJob::dispatch($groupUuid)->delay($this->retryDelay);

        return $results;
    }

    /**
     * Generates a single tag from grouped calls.
     *
     * A tag is made of one "Group UUID" and many "Call UUIDs"
     *
     * @param  string  $groupUuid
     * @return string
     */
    private function generateTag(string $groupUuid): string
    {
        $callUuid = Str::uuid();
        return "{$groupUuid}:{$callUuid}";
    }

    /**
     * Try a new call for every one that still did not answered, increasing number of tries.
     * Will dispatch another retry job on ending
     *
     * @param  string  $groupUuid
     */
    public function retryCallingByGroupUuid(string $groupUuid)
    {
        $retry = [];
        foreach ($this->cache->iterateByGroupUuid($groupUuid) as $tag => $data) {
            if ($data['isSent'] || $data['tries'] > $this->maxCallAttempts) {
                continue;
            }

            $retry[] = [
                'tag' => $tag,
                'url' => $data['url'],
                'number' => $data['number'],
            ];
        }

        foreach ($retry as $call) {
            $this->sendVoiceToNumber($call['tag'], $call['url'], $call['number']);
        }

        if (count($retry) > 0) {
            BandwidthCallRetryJob::dispatch($groupUuid)->delay($this->retryDelay);
        }
    }

    private function sendVoiceToNumber(string $tag, string $url, string $number): PhoneResponse
    {
        $this->cache->incrementCallTries($tag);

        $request = new ApiCreateCallRequest();
        $request->from = $this->voiceFromNumber;
        $request->to = $number;
        $request->applicationId = $this->voiceApplicationId;
        $request->answerUrl = $this->callbackUrl; // path to BXML
        $request->tag = $tag;
        $request->callTimeout = 60;

        Log::debug('Bandwidth Voice API call request', ['request' => $request]);

        try {
            $response = $this->doRequest($request);
            return $this->handleSuccess($response, $number);
        } catch (APIException $e) {
            return $this->handleApiError($e, $tag, $url, $number);
        }
    }

    /**
     * @param  \BandwidthLib\Voice\Models\ApiCreateCallRequest  $request
     * @return \BandwidthLib\Http\ApiResponse
     * @throws \BandwidthLib\APIException
     */
    private function doRequest(ApiCreateCallRequest $request): ApiResponse
    {
        $voiceClient = $this->getBandwidthClient()->getVoice()->getClient();

        $response = $voiceClient->createCall($this->voiceAccountId, $request);

        Log::debug('Bandwidth Voice API call response', ['response' => $response]);

        return $response;
    }

    private function handleSuccess(ApiResponse $item, string $destination): PhoneResponse
    {
        $this->consecutiveErrors = 0;

        $result = $item->getResult();

        $code = $item->getStatusCode();
        $message = ($code == 201) ? 'Call queued' : 'Call failed';
        $id = $result->callId;

        return new PhoneResponse(true, $code, $message, $destination, $id);
    }

    private function handleApiError(
        APIException $e,
        string $tag,
        string $answerUrl,
        string $destination
    ): PhoneResponse
    {
        Log::debug('bandwidth.voice.error', [
            'exception_class' => get_class($e),
            'exception_message' => $e->getMessage(),
            'answer_url' => $answerUrl,
            'destination' => $destination,
        ]);


        if ($e->getCode() == 429) {
            // Too Many Requests
            $this->consecutiveErrors++;
            usleep($this->calculateExponentialBackoffTime($this->consecutiveErrors));
            return $this->sendVoiceToNumber($tag, $answerUrl, $destination);
        }

        return new PhoneResponse(false, 400, $answerUrl, $destination, null);
    }

    /**
     * Retrieve URL from Redis and send as BXML response
     *
     * @param  string  $tag
     * @return string BXML contents
     * @throws \UnexpectedValueException Thrown if key not found
     */
    public function getBxmlAudioByTag(string $tag): string
    {
        $data = $this->cache->get($tag);
        $url = $data['url'];

        if (!$url) {
            throw new UnexpectedValueException('Key not found');
        }

        $playAudio = new PlayAudio($url);
        //$playAudio->username("user");
        //$playAudio->password("pass");
        $response = new Response();
        $response->addVerb($playAudio);
        $response->addVerb(new Hangup());

        $this->cache->markAsSent($tag);

        return $response->toBxml();
    }
}
