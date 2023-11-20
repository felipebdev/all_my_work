<?php 

namespace App\Logs;

use Exception;
use Illuminate\Support\Facades\Log;
abstract class XgrowLog extends Log {

    public static function mail() {
        return Log::channel('mail');
    }

    public static function xError (
        string $message, 
        Exception $error, 
        array $payload = [],
        string $channel = null
    ) :void 
    {
        $channel = self::getChannel($channel);
        Log::channel($channel)
            ->error($message, [
                'error' => [
                    'file' => $error->getFile(),
                    'line' => $error->getLine(),
                    'message' => $error->getMessage(),
                ],
                'payload' => $payload
            ]);
    }

    public static function xInfo(
        string $message,
        array $payload = [],
        string $channel = null
    ) : void 
    {
        $channel = self::getChannel($channel);
        Log::channel($channel)
            ->info($message, [
                'payload' => $payload
            ]);
    }

    protected static function getChannel(string $channel = null) : string {
        $channels = array_keys(config('logging.channels'));
        if (empty($channel) || !in_array($channel, $channels)) {
            $channel = env('LOG_CHANNEL', 'stack');
        }

        return $channel;
    }
    
}