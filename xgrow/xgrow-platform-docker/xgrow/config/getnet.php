<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Variáveis para configuração e utilização do GetNet
    |--------------------------------------------------------------------------
    |
    */

    'local' => [
        'seller_id' => env('GETNET_SELLER_ID', '7e1c3cb1-05ae-4bbf-b6c5-7839f29b8b51'),
        'client_id' => env('GETNET_CLIENT_ID', '0e66f8a9-e59c-4a55-9698-415fc4e49f9b'),
        'secret_id' => env('GETNET_SECRET_ID', '1da90515-23de-4456-8afd-095062c7e175'),
//        'url_api' => env('GETNET_URL_API', 'https://api-homologacao.getnet.com.br/auth/oauth/v2/token'),
        'url_api' => env('GETNET_URL_API', 'https://api-homologacao.getnet.com.br'),
        'url_checkout' => env('GETNET_URL_API', 'https://checkout-homologacao.getnet.com.br'),
        'environment' => env('GETNET_ENVIRONMENT', 'HOMOLOG'),
    ],

    'production' => [
        'seller_id' => env('GETNET_SELLER_ID', 'c98f71f7-3c29-4b8e-ae95-4f05a0cfbace'),
        'client_id' => env('GETNET_CLIENT_ID', '9e5f5f91-8f20-45e6-9d3b-61ea5f171f7f'),
        'secret_id' => env('GETNET_SECRET_ID', 'd0bbf921-057e-418b-9172-b0039c1801ca'),
        'url_api' => env('GETNET_URL_API', 'https://api.getnet.com.br/auth/oauth/v2/token'),
        'url_checkout' => env('GETNET_URL_API', 'https://checkout.getnet.com.br'),
        'environment' => env('GETNET_ENVIRONMENT', 'PRODUCTION'),
    ],

];
