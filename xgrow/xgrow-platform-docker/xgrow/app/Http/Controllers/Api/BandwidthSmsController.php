<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BandwidthSmsController extends Controller
{
    /**
     * Really simple Basic HTTP Authentication validation
     *
     */
    private static function checkAuthBasicOrAbort()
    {
        $user = config('ads.bandwidth.sms.callback_user') ?? null;
        $pass = config('ads.bandwidth.sms.callback_password') ?? null;

        if (!isset($_SERVER['PHP_AUTH_USER'])
            || $_SERVER['PHP_AUTH_USER'] !== $user
            || !isset($_SERVER['PHP_AUTH_PW'])
            || $_SERVER['PHP_AUTH_PW'] !== $pass
        ) {
            $reason = [];

            if (!isset($_SERVER['PHP_AUTH_USER'])) {
                $reason[] = 'AUTH_USER absent';
            }

            if (is_null($user)) {
                $reason[] = 'callback_user is null';
            }

            if ($_SERVER['PHP_AUTH_USER'] !== $user) {
                $reason[] = 'AUTH_USER check failed';
            }

            if (!isset($_SERVER['PHP_AUTH_PW'])) {
                $reason[] = 'AUTH_PW absent';
            }

            if (is_null($pass)) {
                $reason[] = 'callback_password absent';
            }

            if ($_SERVER['PHP_AUTH_PW'] !== $pass) {
                $reason[] = 'AUTH_PW check failed';
            }

            abort(response()->json($reason, 401));
        }
    }

    /**
     *
     *
     * POST /your_url HTTP/1.1
     * Content-Type: application/json; charset=utf-8
     * User-Agent: BandwidthAPI/v2
     *
     * [
     *   {
     *     "type"          : "message-delivered",
     *     "time"          : "2016-09-14T18:20:16Z",
     *     "description"   : "ok",
     *     "to"            : "+12345678902",
     *     "message"       : {
     *       "id"            : "14762070468292kw2fuqty55yp2b2",
     *       "time"          : "2016-09-14T18:20:16Z",
     *       "to"            : ["+12345678902"],
     *       "from"          : "+12345678901",
     *       "text"          : "",
     *       "applicationId" : "93de2206-9669-4e07-948d-329f4b722ee2",
     *       "owner"         : "+12345678902",
     *       "direction"     : "out",
     *       "segmentCount"  : 1
     *     }
     *   }
     * ]
     */
    public function smsCallback(Request $request)
    {
        //self::checkAuthBasicOrAbort();
        $data = $request->all();
        foreach ($data as $item) {
            $direction = $item['message']['direction'] ?? null;
            if ($direction === 'out') {
                $this->handleOut($item);
            } elseif ($direction === 'in') {
                $this->handleIn($item);
            } else {
                Log::error('SMS Callback unknown direction', [
                    'direction' => $direction,
                    'item' => $item
                ]);
            }
        }

        return response()->noContent();
    }

    /**
     * Handle SMS sent from system
     */
    private function handleOut($item): void
    {
        $type = $item['type'] ?? null;

        if ($type == 'message-delivered') {
            $this->handleDelivered($item);
        } elseif ($type == 'message-failed') {
            $this->handleFailed($item);
        } elseif ($type == 'message-sending') {
            $this->handleSending($item);
        } else {
            Log::error('Bandwidth SMS Callback unknown output type', [
                'item' => $item
            ]);
        }
    }

    /**
     * Message Delivered Event
     */
    private function handleDelivered($item): void
    {
        Log::debug('Bandwidth SMS Callback out delivered', [
            'item' => $item
        ]);
    }

    /**
     * Message Failed Event
     */
    private function handleFailed($item): void
    {
        Log::debug('Bandwidth SMS Callback out failed', [
            'item' => $item
        ]);
    }

    /**
     * Message Sending Event (MMS Only)
     */
    private function handleSending($item): void
    {
        Log::debug('Bandwidth SMS Callback out sending', [
            'item' => $item
        ]);
    }

    /**
     * Handle SMS sent from user
     */
    private function handleIn($item): void
    {
        // log only
        Log::debug('Bandwidth SMS Callback input', [
            'item' => $item
        ]);
    }

}
