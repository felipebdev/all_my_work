<?php


use App\Http\Controllers\Api\Mobile\ExpoPushToken;
use App\Http\Controllers\Api\Mobile\MobileConfigurationController;

Route::group(['prefix' => 'mobile', 'namespace' => 'Api\Mobile', 'as' => 'api.mobile',], function () {

    Route::post('login', 'AuthenticateController@authenticate');
    Route::post('logout', 'AuthenticateController@logout');
    Route::post('login-refresh', 'AuthenticateController@refreshToken');
    Route::get('me', 'AuthenticateController@getAuthenticatedUser');

    Route::group(['middleware' => 'auth:mobile'], function () {

        Route::put("expo-push-token", [ExpoPushToken::class, 'update']);

        Route::group(['prefix' => 'configuration'], function () {
            Route::post('', [MobileConfigurationController::class, 'save']);
            Route::get('', [MobileConfigurationController::class, 'show']);
        });

        Route::group(['prefix' => '/platform/{id}'], function ($id) {

            Route::group(['middleware' => 'setplatformid'], function () {

                Route::get('notifications', 'MobileNotificationController@index');
                Route::get('notifications/{notificationId}', 'MobileNotificationController@show');
                Route::put('notifications/{notificationId}', 'MobileNotificationController@update');
                Route::get("products", 'ProductMobileController@getAllProducts');
                Route::get("financial-summary", 'FinancialMobileController@balance');
                Route::get("list-withdrawals", 'FinancialMobileController@listWithdrawals');
                Route::get("financial-report", 'FinancialMobileController@allReporsts');
                Route::post("withdraw", 'FinancialMobileController@withdrawValue');
                Route::get("bank-branches", 'BankMobileController@bankBranches');
                Route::get("bank-information", 'BankMobileController@bankInformation');
                Route::post("update-bank-information", 'BankMobileController@updateBankInformation');
            });
        });
    });
});
