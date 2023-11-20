<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Route;

Route::get('home', 'HomeController@index');

// Login Routes...
Route::get('login', ['as' => 'login', 'uses' => 'Auth\LoginController@showLoginForm']);
Route::post('login', ['as' => 'login.post', 'uses' => 'Auth\LoginController@login']);
Route::post('logout', ['as' => 'logout', 'uses' => 'Auth\LoginController@logout']);
Route::get('logout', 'Auth\LoginController@logout');

// Registration Routes...
Route::get('register', ['as' => 'register', function () {
    abort(499, 'Not available in demo mode.');
}]);
//Route::get('register', ['as' => 'register', 'uses' => 'Auth\RegisterController@showRegistrationForm']);
//Route::post('register', ['as' => 'register.post', 'uses' => 'Auth\RegisterController@register']);

// Password Reset Routes...
Route::group(['prefix' => 'password'], function()
    {
        Route::get('reset', ['as' => 'password.reset', 'uses' => 'Auth\ForgotPasswordController@showLinkRequestForm']);
        Route::post('email', ['as' => 'password.email', 'uses' => 'Auth\ForgotPasswordController@sendResetLinkEmail']);
        Route::get('reset/{token}', ['as' => 'password.reset.token', 'uses' => 'Auth\ResetPasswordController@showResetForm']);
        Route::post('request', ['as' => 'password.request', 'uses' => 'Auth\ResetPasswordController@reset']);
});

// Two factor authentication
Route::get('verify/resend', 'Auth\TwoFactorController@resend')->name('verify.resend');
Route::resource('verify', 'Auth\TwoFactorController')->only(['index', 'store']);

Route::group(['middleware' => 'auth2f'], function () {
    Route::get('', ['as' => 'index', 'uses' => 'HomeController@index']);

    Route::group
    (
        ['prefix' => 'client-transactions'],
        static function () {
            Route::get('', ['as' => 'client-transactions.index', 'uses' => 'ClientTransactionsController@index']);
            Route::get('read', ['as' => 'client-transactions.read', 'uses' => 'ClientTransactionsController@read']);
            Route::patch('info', ['as' => 'client-transactions.info', 'uses' => 'ClientTransactionsController@info']);
            Route::patch('client-data', ['as' => 'client-transactions.client-data', 'uses' => 'ClientTransactionsController@getPlatformsAndProductsByClient']);
        }
    );

    Route::group
    (
        ['prefix' => 'client-dsr'],
        static function () {
            Route::get('', ['as' => 'client-dsr.index', 'uses' => 'ClientDsrController@index']);
            Route::get('read', ['as' => 'client-dsr.read', 'uses' => 'ClientDsrController@read']);
            Route::get('report', ['as' => 'client-dsr.report', 'uses' => 'ClientDsrController@report']);
            Route::patch('info', ['as' => 'client-dsr.info', 'uses' => 'ClientDsrController@info']);
            Route::patch('client-data', ['as' => 'client-dsr.client-data', 'uses' => 'ClientDsrController@getPlatformsAndProductsByClient']);
        }
    );

    Route::group(
        ['prefix' => 'client'],
        function () {
            Route::get('', ['as' => 'client.index', 'uses' => 'ClientController@index']);
            Route::get('create', ['as' => 'client.create', 'uses' => 'ClientController@create']);
            Route::get('edit/{id}', ['as' => 'client.edit', 'uses' => 'ClientController@edit']);
	        Route::post('store', ['as' => 'client.store', 'uses' => 'ClientController@store']);
	        Route::put('update/{id}', ['as' => 'client.update', 'uses' => 'ClientController@update']);
            Route::get('destroy/{id}', ['as' => 'client.destroy', 'uses' => 'ClientController@destroy']);
        }
    );
	Route::group
	(
		['prefix' => 'audit'],
		static function () {
			Route::get('', ['as' => 'audit.index', 'uses' => 'AuditController@index']);
			Route::get('read', ['as' => 'audit.read', 'uses' => 'AuditController@read']);
			Route::patch('info', ['as' => 'audit.info', 'uses' => 'AuditController@info']);
			Route::patch('client-data', ['as' => 'audit.client-data', 'uses' => 'AuditController@getPlatformsAndProductsByClient']);
		}
	);

    Route::group(
        ['prefix' => 'album'],
        function () {
            Route::get('', ['as' => 'gallery.index', 'uses' => 'GalleryController@index']);
            Route::get('create', ['as' => 'gallery.create', 'uses' => 'GalleryController@create']);
            Route::get('edit/{id}', ['as' => 'gallery.edit', 'uses' => 'GalleryController@edit']);
            Route::put('store', ['as' => 'gallery.store', 'uses' => 'GalleryController@store']);
            Route::get('destroy/{id}', ['as' => 'gallery.destroy', 'uses' => 'GalleryController@destroy']);


            Route::group(
                ['prefix' => '{gallery_id}/image'],
                function () {
                Route::get('', ['as' => 'gallery.image.index', 'uses' => 'ImageController@index']);
                Route::post('store', ['as' => 'gallery.image.store', 'uses' => 'ImageController@store']);
                Route::get('destroy/{id}', ['as' => 'gallery.image.destroy', 'uses' => 'ImageController@destroy']);
                }
             );

        }
    );

    Route::group(
        ['prefix' => 'template'],
        function () {
            Route::get('', ['as' => 'template.index', 'uses' => 'TemplateController@index']);
            Route::get('create', ['as' => 'template.create', 'uses' => 'TemplateController@create']);
            Route::get('edit/{id}', ['as' => 'template.edit', 'uses' => 'TemplateController@edit']);
            Route::put('store', ['as' => 'template.store', 'uses' => 'TemplateController@store']);
            //Route::get('destroy/{id}', ['as' => 'template.destroy', 'uses' => 'TemplateController@destroy']);
        }
    );

    Route::group(
        ['prefix' => 'templatePlatform'],
        function () {
            Route::get('', ['as' => 'templatePlatform.index', 'uses' => 'TemplatePlatformController@index']);
            Route::get('create', ['as' => 'templatePlatform.create', 'uses' => 'TemplatePlatformController@create']);
            Route::get('edit/{id}', ['as' => 'templatePlatform.edit', 'uses' => 'TemplatePlatformController@edit']);
            Route::put('store', ['as' => 'templatePlatform.store', 'uses' => 'TemplatePlatformController@store']);
            Route::get('destroy/{id}', ['as' => 'templatePlatform.destroy', 'uses' => 'TemplatePlatformController@destroy']);
        }
    );

    Route::group(
        ['prefix' => 'templateContent'],
        function () {
            Route::get('', ['as' => 'templateContent.index', 'uses' => 'TemplateContentController@index']);
            Route::get('create', ['as' => 'templateContent.create', 'uses' => 'TemplateContentController@create']);
            Route::get('edit/{id}', ['as' => 'templateContent.edit', 'uses' => 'TemplateContentController@edit']);
            Route::put('store', ['as' => 'templateContent.store', 'uses' => 'TemplateContentController@store']);
            //Route::get('destroy/{id}', ['as' => 'template.destroy', 'uses' => 'TemplateController@destroy']);
        }
    );

    Route::group(
        ['prefix' => 'templateCourse'],
        function () {
            Route::get('', ['as' => 'templateCourse.index', 'uses' => 'TemplateCourseController@index']);
            Route::get('create', ['as' => 'templateCourse.create', 'uses' => 'TemplateCourseController@create']);
            Route::get('edit/{id}', ['as' => 'templateCourse.edit', 'uses' => 'TemplateCourseController@edit']);
            Route::put('store', ['as' => 'templateCourse.store', 'uses' => 'TemplateCourseController@store']);
            //Route::get('destroy/{id}', ['as' => 'template.destroy', 'uses' => 'TemplateController@destroy']);
        }
    );

    Route::group(
        ['prefix'=>'admin'],
        function(){
            Route::get('',['as'=>'admin.index','uses'=>'AdminController@index']);
            Route::get('create',['as'=>'admin.create','uses' => 'AdminController@create']);
            Route::get('edit/{id}',['as'=>'admin.edit','uses'=>'AdminController@edit']);
            Route::put('update/{id}',['as'=>'admin.update','uses'=>'AdminController@update']);
            Route::post('store',['as'=>'admin.store','uses'=>'AdminController@store']);
            Route::delete('destroy/{id}',['as'=>'admin.destroy','uses'=>'AdminController@destroy']);
        }
    );



    Route::group(
        ['prefix' => 'platforms'],
        function () {
            Route::get('', ['as' => 'platforms.index', 'uses' => 'PlatformController@index']);
            Route::get('create', ['as' => 'platforms.create', 'uses' => 'PlatformController@create']);
            Route::get('teste-create-folder', ['as' => 'platforms.create-folder', 'uses' => 'PlatformController@testeCreateFolder']);
            Route::get('{id}/edit', ['as' => 'platforms.edit', 'uses' => 'PlatformController@edit']);
            Route::get('{id}/renew', ['as' => 'platforms.renew', 'uses' => 'PlatformController@renew']);
            Route::put('{id}', ['as' => 'platforms.update', 'uses' => 'PlatformController@update']);
            Route::post('', ['as' => 'platforms.store', 'uses' => 'PlatformController@store']);
            Route::delete('{id}', ['as' => 'platforms.destroy', 'uses' => 'PlatformController@destroy']);
        }
    );


    Route::group(
        ['prefix' => 'platforms/users'],
        function () {
            Route::get('', ['as' => 'platforms-users.index', 'uses' => 'PlatformUserController@index']);
            Route::get('create', ['as' => 'platforms-users.create', 'uses' => 'PlatformUserController@create']);
            Route::post('', ['as' => 'platforms-users.store', 'uses' => 'PlatformUserController@store']);
            Route::get('{id}/edit', ['as' => 'platforms-users.edit', 'uses' => 'PlatformUserController@edit']);
            Route::put('{id}', ['as' => 'platforms-users.update', 'uses' => 'PlatformUserController@update']);
            Route::delete('{id}', ['as' => 'platforms-users.destroy', 'uses' => 'PlatformUserController@destroy']);
        }
    );


    Route::group(
        ['prefix' => 'platforms/indicators'],
        function () {
            Route::get('', ['as' => 'platforms-indicators.index', 'uses' => 'PlatformIndicatorController@index']);
        }
    );


    Route::get('acme', 'PlatformController@acme');

    Route::group(
        ['prefix' => 'emails'],
        function () {
            Route::get('', ['as' => 'emails.index', 'uses' => 'EmailController@index']);
            Route::get('create', ['as' => 'emails.create', 'uses' => 'EmailController@create']);
            Route::post('store', ['as' => 'emails.store', 'uses' => 'EmailController@store']);
            Route::get('edit/{id}',['as'=>'emails.edit','uses'=>'EmailController@edit']);
            Route::post('update/{id}',['as'=>'emails.update','uses'=>'EmailController@update']);
            Route::delete('destroy/{id}',['as'=>'emails.destroy','uses'=>'EmailController@destroy']);
        }
    );

    Route::prefix('email-provider')->group(function () {
        Route::get('', ['as' => 'email-provider.index', 'uses' => 'EmailProviderController@index']);
        Route::get('create', ['as' => 'email-provider.create', 'uses' => 'EmailProviderController@create']);
        Route::post('store', ['as' => 'email-provider.store', 'uses' => 'EmailProviderController@store']);
        Route::get('edit/{provider}', ['as' => 'email-provider.edit', 'uses' => 'EmailProviderController@edit']);
        Route::post('update/{provider}', ['as' => 'email-provider.update', 'uses' => 'EmailProviderController@update']);
        Route::delete('destroy/{provider}', ['as' => 'email-provider.destroy', 'uses' => 'EmailProviderController@destroy']);
        Route::post('apply', ['as' => 'email-provider.apply', 'uses' => 'EmailProviderController@apply']);
    });

    Route::group(
        ['prefix' => 'configs'],
        function () {
            Route::get('',['as'=>'configs.edit','uses'=>'ConfigController@edit']);
            Route::post('update',['as'=>'configs.update','uses'=>'ConfigController@update']);
        }
    );

    Route::group(
        ['prefix' => 'products'],
        function () {
            Route::get('/', ['as' => 'products.index', 'uses' => 'ProductController@index']);
            Route::get('{id}/show', ['as' => 'products.show', 'uses' => 'ProductController@show']);
            Route::post('{id}/change_status', ['as' => 'products.change.status', 'uses' => 'ProductController@changeStatus']);
        }
    );

    Route::group(
        ['prefix' => 'services', 'as' => 'services.'],
        function () {
            Route::get('/', ['as' => 'index', 'uses' => 'ServiceController@index']);
            Route::get('/create', ['as' => 'create', 'uses' => 'ServiceController@create']);
            Route::post('/', ['as' => 'store', 'uses' => 'ServiceController@store']);
            Route::get('/{uuid}/edit', ['as' => 'edit', 'uses' => 'ServiceController@edit']);
            Route::put('/{uuid}', ['as' => 'update', 'uses' => 'ServiceController@update']);
            Route::delete('/{uuid}', ['as' => 'delete', 'uses' => 'ServiceController@destroy']);
        }
    );

	Route::group
	(
		['prefix' => 'test'],
		static function ()
		{
			Route::get('/',['as' => 'create', 'uses'=> 'TestController@index']);
			Route::post('/', ['as' => 'store', 'uses' => 'TestController@store']);
			Route::get('/create', ['as' => 'test.create', 'uses' => 'TestController@create']);
			Route::get('/{id}/edit', ['as' => 'edit', 'uses' => 'TestController@edit']);
			Route::put('/{id}', ['as' => 'update', 'uses' => 'TestController@update']);
			Route::delete('/{id}', ['as' => 'delete', 'uses' => 'TestController@destroy']);
		}
	);

	Route::group
	(
		[
            'prefix' => 'chargeback', 'as' => 'chargeback.'
        ],
		function ()
		{
			Route::get('/',['as' => 'create', 'uses'=> 'ChargebackController@create']);
			Route::post('/', ['as' => 'store', 'uses' => 'ChargebackController@store']);
		}
	);

});
