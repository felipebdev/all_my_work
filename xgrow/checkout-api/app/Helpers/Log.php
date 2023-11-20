<?php
namespace App\Helpers;
use Exception;

class Log
{
    /**
     * Log an exception message to the logs.
     *
     * @param  Exception  $e
     * @param $name
     * @param  array  $details
     */
    public function exception($name, Exception $e, array $details): void
    {
        $message = $e->getMessage();
        $regex = '/([?&])(api_key=)([^&`]+)/';
        $replacement = '$1$2XXXX';
        $formattedMessage = preg_replace($regex, $replacement, $message);

        $defaults = [
            'reason' => $formattedMessage,
            'code' => $e->getCode(),
            'line' => $e->getLine(),
            'file' => $e->getFile()

        ];

        $info = $details + $defaults;
        \Illuminate\Support\Facades\Log::error($name, $info);
    }
}
