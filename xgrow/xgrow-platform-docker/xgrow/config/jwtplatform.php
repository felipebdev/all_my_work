<?php

return [
    'jwt_checkout' => env('JWT_CHECKOUT'),
    'jwt_platform' => env('JWT_PLATFORM'),
    'jwt_web' => env('JWT_WEB'),
    'jwt_mobile_api_key' => env('API_LA_MOBILE_KEY'),

    'jwt_report_dev_key' => env('REPORT_DEV_KEY'),
    'jwt_report_prod_key' => env('REPORT_PROD_KEY'),

    /** JWT FOR CLEAN LA CACHE */
    'jwt_clean_cache_la' => env('JWT_CLEAN_CACHE_LA'),

    /** JWT FOR API REPORT */
    'jwt_report_api_key' => env('REPORT_API_KEY'),
];
