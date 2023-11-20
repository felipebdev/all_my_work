<?php

return [
    /** LA and Content API*/
    'url' => env('API_LA_CONTENT_URI', 'https://content-api.la-dev.xgrow.com/graphql'),
    'file_url' => env('API_LA_FILE_CONTENT_URI', 'https://content-api.la-dev.xgrow.com/files/create'),

    'sentry_dsn' => env('SENTRY_DSN_FRONTEND'),
    'sentry_environment' => env('SENTRY_ENVIRONMENT'),

    'url_config' => env('LA_PLATFORM_CONFIGURATION_API', 'https://intense-api.la-dev.xgrow.com/v1/api/platform-config'),
    'url_config_token' => env('LA_PLATFORM_CONFIGURATION_TOKEN'),

    'url_oauth' => env('API_OAUTH_URL'),

    #Homolog
    'url_config_homolog' => env('LA_PLATFORM_CONFIGURATION_HMG_API', 'https://intense-api.la-dev.xgrow.com/v1/api/platform-config')
];
