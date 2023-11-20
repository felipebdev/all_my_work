<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'namespace' => 'Modules\Integration\Controllers',
    'middleware' => ['web', 'auth2f', 'audit.route', 'verify.ip', 'integration'],
    'prefix' => '/apps/integrations',
    'as' => 'apps.integrations.'
], function () {
    Route::get('/', ['as' => 'index', 'uses' => 'AppIntegrationController@index']);
    Route::post('/', ['as' => 'store', 'uses' => 'AppIntegrationController@store']);
    Route::get('/{integration}', ['as' => 'show', 'uses' => 'AppIntegrationController@show']);
    Route::put('/{integration}', ['as' => 'update', 'uses' => 'AppIntegrationController@update']);
    Route::delete('/{integration}', ['as' => 'destroy', 'uses' => 'AppIntegrationController@destroy']);
    
    Route::group([
        'prefix' => '{integration}/actions', 
        'as' => 'actions.'
    ], function () {
        Route::get('/', ['as' => 'index', 'uses' => 'ActionController@index']);
        Route::post('/', ['as' => 'store', 'uses' => 'ActionController@store']);
        Route::get('/{action}', ['as' => 'show', 'uses' => 'ActionController@show']);
        Route::put('/{action}', ['as' => 'update', 'uses' => 'ActionController@update']);
        Route::delete('/{action}', ['as' => 'destroy', 'uses' => 'ActionController@destroy']);
    });
    
    Route::group([
        'prefix' => '{integration}/logs', 
        'as' => 'logs.'
    ], function () {
        Route::get('/', ['as' => 'index', 'uses' => 'LogController@index']);
        Route::get('/{id}', ['as' => 'show', 'uses' => 'LogController@show']);
        Route::post('/{id}', ['as' => 'reprocess', 'uses' => 'LogController@reprocess']);
    });

    Route::get('/{integration}/{provider}/{resource}', ['as' => 'metadata', 'uses' => 'AppIntegrationController@metadata']);
});

Route::group([
    'namespace' => 'Modules\Integration\Controllers',
    'prefix' => '/apps/integrations/oauth',
    'as' => 'apps.integrations.oauth.'
], function () {
    Route::get('/callback', ['as' => 'callback', 'uses' => 'OAuthController@callback']);
});

/**
 * Dispatch event example
 */
// Route::get('/handle', function (){
//     $payment = App\Payment::findOrFail(806);
//     $paymentData = new Modules\Integration\Events\PaymentData($payment);

//     Modules\Integration\Queue\Jobs\HandleIntegration::dispatchNow(
//         Modules\Integration\Enums\EventEnum::PAYMENT_APPROVED,
//         $payment->platform_id,
//         [101,102],
//         $paymentData
//     );
// });
