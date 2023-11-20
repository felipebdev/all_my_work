<?php

/**
 * Configurations for third-party advertising Services
 */

return [
    'bandwidth' => [
        'sms' => [
            'username' => env('BANDWIDTH_SMS_USERNAME'),
            'password' => env('BANDWIDTH_SMS_PASSWORD'),
            'application_id' => env('BANDWIDTH_SMS_APP_ID'),
            'from_number' => env('BANDWIDTH_SMS_FROM_NUMBER'),
            'account_id'  => env('BANDWIDTH_SMS_ACCOUNT_ID'),
            'callback_user'  => env('BANDWIDTH_SMS_CALLBACK_USER'),
            'callback_password'  => env('BANDWIDTH_SMS_CALLBACK_PASSWORD'),
        ],
        'voice' => [
            'username' => env('BANDWIDTH_VOICE_USERNAME'),
            'password' => env('BANDWIDTH_VOICE_PASSWORD'),
            'application_id' => env('BANDWIDTH_VOICE_APP_ID'),
            'from_number' => env('BANDWIDTH_VOICE_FROM_NUMBER'),
            'account_id'  => env('BANDWIDTH_VOICE_ACCOUNT_ID'),
            'callback_user'  => env('BANDWIDTH_VOICE_CALLBACK_USER'),
            'callback_password'  => env('BANDWIDTH_VOICE_CALLBACK_PASSWORD'),
        ],
    ],

    /**
     * BulkGate production configuration
     */
    'bulkgate' => [
        'api_host' => env('BULKGATE_HOST', 'https://portal.bulkgate.com'),
        'api_sms_path' => env('BULKGATE_SMS_PATH', '/api/1.0/simple/promotional'),
        'application_id' => env('BULKGATE_APP_ID'),
        'application_token' => env('BULKGATE_APP_TOKEN'),
    ],

    'zenvia' => [
        'api_host' => env('ZENVIA_HOST', 'https://api2.totalvoice.com.br'),
        'api_voice_path' => env('ZENVIA_VOICE_PATH', '/audio/'),
        'application_token' => env('ZENVIA_APP_TOKEN'),
    ],
];

