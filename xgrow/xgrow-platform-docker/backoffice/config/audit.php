<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Audit with Elastic Defaults
    |--------------------------------------------------------------------------
    |
    | This options is about the elastic search audit for laravel
    | This audit is not Laravel Auditing
    |
    */

    'enabled' => env('AUDIT_ENABLED', false),

    'drive' => [
        'host' => [
            'scheme' => env('AUDIT_SCHEME', 'http'),
            'port' => env('AUDIT_PORT', '9200'),
            'host' => env('AUDIT_HOST', 'localhost'),
            'user' => env('AUDIT_USER', ''),
            'pass' => env('AUDIT_PASS', ''),
        ],
        'index' => env('AUDIT_INDEX', 'laravel_auditing'),
        'queue' => env('AUDIT_QUEUE', false)
    ],
];
