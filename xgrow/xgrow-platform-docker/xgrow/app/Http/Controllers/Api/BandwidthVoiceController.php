<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Bandwidth\BandwidthVoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use UnexpectedValueException;

class BandwidthVoiceController extends Controller
{
    /**
     * Really simple Basic HTTP Authentication validation
     *
     */
    private static function checkAuthBasicOrAbort()
    {
        $user = config('ads.bandwidth.voice.callback_user') ?? null;
        $pass = config('ads.bandwidth.voice.callback_password') ?? null;

        if (!isset($_SERVER['HTTP_AUTH_USER'])
            || $_SERVER['HTTP_AUTH_USER'] !== $user
            || !isset($_SERVER['PHP_AUTH_PW'])
            || $_SERVER['PHP_AUTH_PW'] !== $pass
        ) {
            abort(response()->json('Unauthorized', 401));
        }
    }

    private $bandwidthVoiceService;

    public function __construct(BandwidthVoiceService $bandwidthVoiceService)
    {
        $this->bandwidthVoiceService = $bandwidthVoiceService;
    }

    public function voiceTest(Request $request, $telephoneNumber)
    {
        abort_if($request->token != 'jX8uDB9BZcGShNczd4TCLuFzZ0BDXcjfxbtnMqHLhn4', 401);

        $results = $this->bandwidthVoiceService->sendVoiceToNumbers('https://testev2.xgrow.com/audioteste.mp3', [$telephoneNumber]);

        Log::debug('Bandwidth Voice test', ['request' => $request]);

        return response()->json($results);
    }

    public function voiceInitCallback(Request $request)
    {
        Log::debug('Bandwidth Voice Init Callback', ['params' => $request->all(), 'request' => $request]);
        return response()->noContent();
    }

    /**
     * Voice Callback from Bandwidth
     *
     * Example: Answer event with tag property
     * POST http://[External server URL]
     * {
     *      "eventType"     : "answer",
     *      "eventTime"     : "2019-06-20T15:54:25.435Z",
     *      "accountId"     : "55555555",
     *      "applicationId" : "7fc9698a-b04a-468b-9e8f-91238c0d0086",
     *      "from"          : "+15551112222",
     *      "to"            : "+15553334444",
     *      "direction"     : "outbound",
     *      "callId"        : "c-95ac8d6e-1a31c52e-b38f-4198-93c1-51633ec68f8d",
     *      "callUrl"       : "https://voice.bandwidth.com/api/v2/accounts/55555555/calls/c-95ac8d6e-1a31c52e-b38f-4198-93c1-51633ec68f8d",
     *      "startTime"     : "2019-06-20T15:54:22.234Z",
     *      "answerTime"    : "2019-06-20T15:54:25.432Z",
     *      "tag"           : "example-tag"
     * }
     */
    public function voiceStatusCallback(Request $request)
    {
        Log::debug('Bandwidth Voice Status Callback', ['params' => $request->all(), 'request' => $request]);

        //self::checkAuthBasicOrAbort();
        $eventType = $request->eventType;
        if ($eventType == 'answer') {
            return $this->handleAnswer($request);
        } elseif ($eventType == 'disconnect') {
            return $this->handleDisconnect($request);
        }

        Log::error('Bandwidth Voice Callback unknown request', [
            'request' => $request
        ]);

        return response()->json('Invalid request', 422);
    }

    private function handleAnswer(Request $request)
    {
        $tag = $request->tag;
        try {
            $bxml = $this->bandwidthVoiceService->getBxmlAudioByTag($tag);
            return response()->make($bxml, 200)->header('Content-Type', 'application/xml;charset=UTF-8');
        } catch (UnexpectedValueException $e) {
            Log::error('Bandwidth Voice Callback invalid UUID', [
                'request' => $request
            ]);

            return response()->json('UUID not found', 404);
        }
    }

    private function handleDisconnect(Request $request)
    {
        $cause = $request->cause;

        Log::debug('Bandwidth Voice Callback disconnected', [
            'cause' => $cause,
            'request' => $request
        ]);

        return response()->noContent();
    }

    public function voiceInitFallback(Request $request)
    {
        Log::debug('Bandwidth Voice Init Fallback', ['params' => $request->all(), 'request' => $request]);
        return response()->noContent();
    }

}
