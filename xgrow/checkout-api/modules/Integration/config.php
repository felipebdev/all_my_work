<?php

return [
    /**
     * Module name
     */
    'module' => 'apps',

    /**
     * Consumer queue name
     */
    'queue' => 'apps',

    /**
     * Services config
     */
    'services' => [
        'infusion' => [
            'app_id' => env('INFUSIONSOFT_APP_ID'),
            'app_key' => env('INFUSIONSOFT_APP_KEY'),
            'app_secret' => env('INFUSIONSOFT_APP_SECRET')
        ]
        ],

    /**
     * Databases
     */
    'database' => [
        'mongo' => [
            'uri' => env('MONGODB_APPS_CONNECTION', 'mongodb://localhost:27017'),
            'db' => env('MONGODB_APPS_DATABASE', 'xgrow-apps'),
            'collection' => env('MONGODB_APPS_COLLECTION', 'logs'),
            'ssl' => env('MONGODB_APPS_SSL', false),
            'cert_path' => env('MONGODB_APPS_CERT_PATH', '')
        ]
    ]
];
