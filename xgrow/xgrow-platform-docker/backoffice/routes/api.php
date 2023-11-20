<?php

use App\Http\Controllers\Api\{
    AuthApiController,
    BackActionController,
    BackPermissionController,
    BackRoleController,
    ClientController,
    ClientWithdrawalsController,
    ConfigController,
    EmailController,
    EmailProviderController,
    PaymentController,
    PlanController,
    PlatformController,
    PlatformUserController,
    ProductController,
    ReportController,
    ServiceController,
    SubscriberController,
    UserController
};
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::get('teste', function () {
//    $data = [
//        'clients_names' => [3]
//    ];
//    $req = (new \App\Services\ReportAPI\ReportAPIService())->transactionsReport($data);
//    dd($req);
//});

Route::post('/authenticate', [AuthApiController::class, 'authenticate'])->name('api.auth.authenticate');
Route::get('/new-random-password', [AuthApiController::class, 'newRandomPassword'])->name('api.auth.new-random-password');

Route::group(['middleware' => ['auth.api', 'cors']], function () {
    Route::post('/token-validate', [AuthApiController::class, 'tokenValidate'])->name('api.auth.token-validate');
    Route::post('/token-logout', [AuthApiController::class, 'tokenLogout'])->name('api.auth.token-logout');

    Route::middleware('role:report')->group(function () {

        Route::get('product/list', [ProductController::class, 'list'])->name('api.product.list');
        Route::get('platform/list', [PlatformController::class, 'list'])->name('api.platform.list');
        Route::get('client/list', [ClientController::class, 'list'])->name('api.client.list');
        Route::get('plan/list', [PlanController::class, 'list'])->name('api.plan.list');
        Route::get('subscriber/list', [SubscriberController::class, 'list'])->name('api.subscriber.list');
        Route::get('payment/list', [PaymentController::class, 'list'])->name('api.payment.list');

        Route::prefix('report')->group(function () {
            Route::get('transactions', [ReportController::class, 'transactionsReport'])->name('api.report.transactions');
        });

        Route::group(
            ['prefix' => 'client-transactions'],
            static function () {
                Route::get('', ['as' => 'client-transactions.index', 'uses' => 'Api\ClientTransactionsController@index']);
                Route::get('read', ['as' => 'client-transactions.read', 'uses' => 'Api\ClientTransactionsController@read']);
                Route::get('client-data', ['as' => 'client-transactions.client-data', 'uses' => 'Api\ClientTransactionsController@getPlatformsAndProductsByClient']);
            }
        );

        Route::group(
            ['prefix' => 'audit'],
            static function () {
                Route::get('', ['as' => 'audit.index', 'uses' => 'Api\AuditController@index']);
                Route::get('read', ['as' => 'audit.read', 'uses' => 'Api\AuditController@read']);
            }
        );

        Route::group(
            ['prefix' => 'client-dsr'],
            static function () {
                Route::get('', ['as' => 'client-dsr.index', 'uses' => 'Api\ClientDsrController@index']);
                Route::get('read', ['as' => 'client-dsr.read', 'uses' => 'Api\ClientDsrController@read']);
                Route::get('report', ['as' => 'client-dsr.report', 'uses' => 'Api\ClientDsrController@report']);
            }
        );
    });

    Route::prefix('client')->middleware('role:client')->group(function () {
        //Restricted access by action
        Route::post('', [ClientController::class, 'store'])->name('api.client.store')->middleware('action:client-store');
        Route::put('{id}', [ClientController::class, 'update'])->name('api.client.update')->middleware('action:client-update');
        Route::delete('{id}', [ClientController::class, 'destroy'])->name('api.client.destroy')->middleware('action:client-destroy');
        Route::middleware('action:client-export')->group(function () {
            Route::get('export', [ClientController::class, 'exportClient'])->name('api.export.client');
            Route::get('{id}/export-platform', [ClientController::class, 'exportClientPlatform'])->name('export.client.platform');
            Route::get('{id}/export-product', [ClientController::class, 'exportClientProduct'])->name('export.client.product');
        });

        //Minimal role access (listing and view)
        Route::get('summary', [ClientController::class, 'getGeneralClientStats'])->name('api.get.client.summary');
        Route::get('{clientId}/stats', [ClientController::class, 'getClientStats'])->name('api.get.client.stats');
        Route::get('{id}/platform', [ClientController::class, 'platform'])->name('client.platform');
        Route::get('{id}/product', [ClientController::class, 'product'])->name('client.product');
        Route::get('{id}/product/{productId}', [ClientController::class, 'productById'])->name('client.product.by.id');
        Route::get('{id}/summary', [ClientController::class, 'summaryClient'])->name('client.summary');
        Route::get('get-by-name', [ClientController::class, 'getByName'])->name('api.get.client.by.name');
        Route::get('{id}/withdrawals', [ClientWithdrawalsController::class, 'index'])->name('api.client.with.drawals');
        Route::get('withdrawal/{id}', [ClientWithdrawalsController::class, 'show'])->name('api.get.client.with.drawals');
        Route::get('{id}', [ClientController::class, 'show'])->name('api.client.show');
        Route::get('', [ClientController::class, 'index'])->name('api.client.index');
    });

    Route::prefix('subscriber')->middleware('role:subscriber')->group(function () {
        //Restricted access by action
        Route::post('', [SubscriberController::class, 'store'])->name('api.subscriber.store')->middleware('action:subscriber-store');
        Route::post('resend-data/{id}', [SubscriberController::class, 'resendData'])->name('api.subscriber.resend.data');

        Route::middleware('action:subscriber-update')->group(function () {
            Route::put('{id}', [SubscriberController::class, 'update'])->name('api.subscriber.update');
            Route::put('{id}/change-status', [SubscriberController::class, 'changeStatus'])->name('api.subscriber.change.status');
        });

        Route::delete('{id}', [SubscriberController::class, 'destroy'])->name('api.subscriber.destroy')
            ->middleware('action:subscriber-destroy');
        Route::get('export', [SubscriberController::class, 'export'])->name('api.subscriber.export')
            ->middleware('action:subscriber-export');

        //Minimal role access (listing and view)
        Route::get('get-by-name', [SubscriberController::class, 'getByName'])->name('api.get.subscriber.by.name');
        Route::get('summary', [SubscriberController::class, 'summary'])->name('api.subscriber.summary');
        Route::get('{id}', [SubscriberController::class, 'show'])->name('api.subscriber.show');
        Route::get('', [SubscriberController::class, 'index'])->name('api.subscriber.index');
    });


    Route::prefix('dashboard')->middleware('role:dashboard')->group(function () {
        Route::get('', ['as' => 'api.dashboard.summary', 'uses' => 'Api\DashboardController@summary']);
        Route::get('sales-summary', ['as' => 'api.dashboard.sale-summary', 'uses' => 'Api\DashboardController@salesSummary']);
        Route::get('sales-graph', ['as' => 'api.dashboard.sale-graph', 'uses' => 'Api\DashboardController@salesGraph']);
    });

    Route::middleware('role:setting')->group(function () {

        Route::prefix('email-provider')->group(function () {
            //Restricted access by action
            Route::middleware('action:setting-store')->group(function () {
                Route::post('', [EmailProviderController::class, 'store'])
                    ->name('api.setting.email-provider.store');
                Route::post('apply', [EmailProviderController::class, 'apply'])
                    ->name('api.settings.email-provider.apply');
            });
            Route::put('{id}', [EmailProviderController::class, 'update'])->name('api.setting.email-provider.update')
                ->middleware('action:setting-update');
            Route::delete('{id}', [EmailProviderController::class, 'destroy'])->name('api.setting.email-provider.destroy')
                ->middleware('action:setting-destroy');
            Route::get('export', [EmailProviderController::class, 'export'])->name('api.setting.email-provider.export')
                ->middleware('action:setting-export');

            //Minimal role access (listing and view)
            Route::get('get-drivers', [EmailProviderController::class, 'getDrivers'])
                ->name('api.settings.email-provider.get-drivers');
            Route::get('', [EmailProviderController::class, 'index'])->name('api.setting.email-provider.index');
            Route::get('get-data-provider', [EmailProviderController::class, 'getDataProvider'])->name('api.setting.email-provider.data');
            Route::get('{id}', [EmailProviderController::class, 'show'])->name('api.settings.email-provider.show');
        });


        Route::prefix('email')->group(function () {
            //Restricted access by action
            Route::post('', [EmailController::class, 'store'])->name('api.setting.email.store')
                ->middleware('action:setting-store');
            Route::put('{id}', [EmailController::class, 'update'])->name('api.setting.email.update')
                ->middleware('action:setting-update');
            Route::delete('{id}', [EmailController::class, 'destroy'])->name('api.setting.email.destroy')
                ->middleware('action:setting-destroy');
            Route::get('export', [EmailController::class, 'export'])->name('api.setting.email.export')
                ->middleware('action:setting-export');

            //Minimal role access (listing and view)
            Route::get('{id}', [EmailController::class, 'show'])->name('api.setting.email.show');
            Route::get('', [EmailController::class, 'index'])->name('api.setting.email.index');
        });

        Route::group(
            ['prefix' => 'configs'],
            function () {
                Route::get('', [ConfigController::class, 'show'])->name('api.setting.show');
                Route::put('update', [ConfigController::class, 'update'])
                    ->name('api.setting.update')
                    ->middleware('action:setting-update');
            }
        );

        Route::prefix('service')->group(function () {
            //Restricted access by action
            Route::post('', [ServiceController::class, 'store'])->name('api.setting.service.store')
                ->middleware('action:setting-store');
            Route::put('{id}', [ServiceController::class, 'update'])->name('api.setting.service.update')
                ->middleware('action:setting-update');
            Route::delete('{id}', [ServiceController::class, 'destroy'])->name('api.setting.service.destroy')
                ->middleware('action:setting-destroy');

            //Minimal role access (listing and view)
            Route::get('{id}', [ServiceController::class, 'show'])->name('api.setting.service.show');
            Route::get('', [ServiceController::class, 'index'])->name('api.setting.service.index');
        });
    });


    Route::apiResource('subscriber-product', 'Api\SubscriberProductController');

    Route::middleware('role:user|permissions')->group(function () {
        Route::get('permission/list', ['as' => 'api.role.list', 'uses' => 'Api\BackPermissionController@list']);
    });

    Route::middleware('role:permissions')->group(function () {

        Route::prefix('permission')->group(function () {
            //Restricted access by action
            Route::post('', [BackPermissionController::class, 'store'])->name('api.permission.store')
                ->middleware('action:permissions-store');
            Route::put('{id}', [BackPermissionController::class, 'update'])->name('api.permission.update')
                ->middleware('action:permissions-update');
            Route::delete('{id}', [BackPermissionController::class, 'destroy'])->name('api.permission.destroy')
                ->middleware('action:permissions-destroy');

            //Minimal role access (listing and view)
            Route::get('{id}', [BackPermissionController::class, 'show'])->name('api.permission.show');
            Route::get('', [BackPermissionController::class, 'index'])->name('api.permission.index');
        });

        Route::get('role/list', [BackRoleController::class, 'list'])->name('api.role.list');
        Route::get('action/list', [BackActionController::class, 'list'])->name('api.action.list');
        Route::get('user/list', [UserController::class, 'list'])->name('api.user.list');
    });

    Route::prefix('user')->middleware('role:user')->group(function () {

        //Restricted access by action
        Route::post('', [UserController::class, 'store'])->name('api.user.store')
            ->middleware('action:user-store');
        Route::put('{id}', [UserController::class, 'update'])->name('api.user.update')
            ->middleware('action:user-update');
        Route::delete('{id}', [UserController::class, 'destroy'])->name('api.user.destroy')
            ->middleware('action:user-destroy');

        Route::patch('{id}/change-status', [UserController::class, 'changeStatus'])
            ->name('api.user.index')
            ->middleware('action:user-update');

        //Minimal role access (listing and view)
        Route::get('{id}', [UserController::class, 'show'])->name('api.user.show');
        Route::get('', [UserController::class, 'index'])->name('api.user.index');
    });

    Route::middleware('role:platform')->group(function () {

        Route::prefix('platform')->group(function () {

            //Restricted access by action
            Route::post('', [PlatformController::class, 'store'])->name('api.platform.store')
                ->middleware('action:platform-store');
            Route::put('{id}', [PlatformController::class, 'update'])->name('api.platform.update')
                ->middleware('action:platform-update');
            Route::delete('{id}', [PlatformController::class, 'destroy'])->name('api.platform.destroy')
                ->middleware('action:platform-destroy');

            //Minimal role access (listing and view)
            Route::get('get-by-name', [PlatformController::class, 'getByName'])->name('api.get.platform.by.name');
            Route::get('summary', [PlatformController::class, 'summary'])->name('api.get.summary');
            Route::get('{id}/permission', [PlatformController::class, 'getPermissions'])->name('platform.permissions');
            Route::get('{id}/product', [PlatformController::class, 'getProducts'])->name('platform.products');
            Route::get('{id}', [PlatformController::class, 'show'])->name('api.platform.show');
            Route::get('', [PlatformController::class, 'index'])->name('api.platform.index');
        });


        Route::prefix('platform_user')->group(function () {

            //Restricted access by action
            Route::post('', [PlatformUserController::class, 'store'])->name('api.platform_user.store')
                ->middleware('action:platform-store');

            Route::prefix('{id}')->group(function () {
                Route::match(array('PUT', 'PATCH'), '', [PlatformUserController::class, 'update'])->name('api.platform_user.update')
                    ->middleware('action:platform-update');
                Route::delete('', [PlatformUserController::class, 'destroy'])->name('api.platform_user.destroy')
                    ->middleware('action:platform-destroy');
                Route::patch('restore', [PlatformUserController::class, 'restore'])->name('api.platform_user.restore')
                    ->middleware('action:platform-update');
            });

            //Minimal role access (listing and view)
            Route::get('{id}', [PlatformUserController::class, 'show'])->name('api.platform_user.show');
            Route::get('', [PlatformUserController::class, 'index'])->name('api.platform_user.index');
        });
    });


    Route::prefix('product')->middleware('role:product')->group(function () {
        //Restricted access by action
        Route::post('', [ProductController::class, 'store'])->name('api.product.store')
            ->middleware('action:product-store');
        Route::middleware('action:product-update')->group(function () {
            Route::put('{id}', [ProductController::class, 'update'])->name('api.product.update');
            Route::patch('{id}/change-status', [ProductController::class, 'changeStatus'])->name('products.change.status');
        });
        Route::delete('{id}', [ProductController::class, 'destroy'])->name('api.product.destroy')
            ->middleware('action:product-destroy');
        Route::get('export', [ProductController::class, 'export'])->name('api.product.export')
            ->middleware('action:product-export');

        //Minimal role access (listing and view)
        Route::get('summary', [ProductController::class, 'summary'])->name('api.product.summary');
        Route::get('{id}', [ProductController::class, 'show'])->name('api.product.show');
        Route::get('', [ProductController::class, 'index'])->name('api.product.index');
        Route::get('{id}/transactions', [ProductController::class, 'getTransactionsByProductId'])->name('api.clients.transaction.byproduct');
    });

    Route::post('/profile', [AuthApiController::class, 'getProfile'])->name('api.auth.profile');
    Route::put('/profile', [AuthApiController::class, 'updateProfile'])->name('api.auth.profile.update');
});
