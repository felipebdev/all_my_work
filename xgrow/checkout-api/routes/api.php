<?php

use Illuminate\Http\Request;

//use DB;
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

// New Learning Area
Route::group(['middleware' => ['api']], function () {

    Route::post('request-token', 'Api\TokenController@requestToken')->name('request-token');

    Route::middleware(['middleware' => 'jwt.checkout'])->group(function () {
        Route::post('events/add-payment-info', 'Api\EventsController@addPaymentInfo')->name('events.add-payment-info');
        Route::post('events/add-to-cart', 'Api\EventsController@addToCart')->name('events.add-to-cart');
        Route::post('events/complete-registration', 'Api\EventsController@completeRegistration')->name('events.complete-registration');
        Route::post('events/contact', 'Api\EventsController@contact')->name('events.contact');
        Route::post('events/initiate-checkout', 'Api\EventsController@initiateCheckout')->name('events.initiate-checkout');
        Route::post('events/lead', 'Api\EventsController@lead')->name('events.lead');
        Route::post('events/page-view', 'Api\EventsController@pageView')->name('events.page-view');
        Route::post('events/purchase', 'Api\EventsController@purchase')->name('events.purchase');
        Route::post('events/subscribe', 'Api\EventsController@subscribe')->name('events.subscribe');
        Route::post('events/view-content', 'Api\EventsController@viewContent')->name('events.view-content');
    });

    Route::group(['middleware' => 'jwt.platform'], function () {
        Route::get('user-info', 'Api\SubscriberController@userInfo')->name('user-info');

        Route::get('payments', [\App\Http\Controllers\Api\PaymentController::class, 'index'])->name('payments');
        Route::post('payments/recurrence', [\App\Http\Controllers\Api\PaymentController::class, 'recurrenceOrder'])->name('payments.recurrence');
        Route::post('payments/unlimited', [\App\Http\Controllers\Api\PaymentController::class, 'unlimitedOrder'])->name('payments.unlimited');
        Route::get('payments/{payment_id}/logs', [\App\Http\Controllers\Api\PaymentLogController::class, 'index'])->name('payments.logs');

        // Atualizar dados do cartão de crédito
        Route::get('creditcard', ['as' => 'creditcard.list', 'uses' => 'Mundipagg\CreditCardController@listCreditCards']);
        Route::get('creditcard/{id}', ['as' => 'creditcard.get', 'uses' => 'Mundipagg\CreditCardController@getCreditCard']);

        // Store new card
        Route::post('creditcard', ['as' => 'creditcard.store', 'uses' => 'Mundipagg\CreditCardController@storeCreditCard']);
        Route::post('creditcard/{id}', ['as' => 'creditcard.store', 'uses' => 'Mundipagg\CreditCardController@changeDefaultCreditCard']);

        Route::delete('creditcard/{id}', ['as' => 'creditcard.destroy', 'uses' => 'Mundipagg\CreditCardController@deleteCreditCard']);
    });
});

Route::group(['prefix' => 'students', 'middleware' => 'api.students.access'], function () {
    // Students Routes
    Route::post('/refund-by-students', [\App\Http\Controllers\Api\RefundController::class, 'storeByStudents']);
    Route::post('/refund-by-students/sendcode', [\App\Http\Controllers\Api\RefundController::class, 'sendTwoFactorCode'])->name('students.twofactor.sendcode');
    Route::get('/refund-by-students/checkcode', [\App\Http\Controllers\Api\RefundController::class, 'checkTwoFactorCode'])->name('students.twofactor.checkcode');

    Route::get('test', function () {
        return response()->json([
            "error" => false,
            'message' => 'Teste Ok!',
            'response' => ['Teste Ok']
        ], 200);
    });

    Route::post('payments/methods', [\App\Http\Controllers\Api\Students\StudentsPaymentController::class, 'change'])
        ->name('la.payments.methods');

    Route::get('products/{product_id}/change', [\App\Http\Controllers\Api\Students\StudentsPlanChangeController::class, 'listChangePlans'])
        ->name('la.plans.change.list');

    Route::post('products/{product_id}/change', [\App\Http\Controllers\Api\Students\StudentsPlanChangeController::class, 'storePlan'])
        ->name('la.plans.change.store');

});

Route::group(['prefix' => 'payment-area', 'middleware' => 'api.recurrence.access'], function () {

    Route::get('recurrence/{recurrence_id}', [\App\Http\Controllers\Api\Students\StudentsRecurrenceController::class, 'getStudentsRecurrenceById'])
        ->name('payment-area.recurrence.get');

    Route::post('recurrence/{recurrence_id}', [\App\Http\Controllers\Api\Students\StudentsRecurrenceController::class, 'generateTransactionByRecurrence'])
        ->name('payment-area.recurrence.generate');

});

// Web Platform

Route::prefix('payments/{payment_id}/failed/')->middleware(['jwt.web:manual_payment'])->group(function () {
    Route::post('/', [\App\Http\Controllers\Api\PaymentFailedController::class, 'update'])->name('payments.failed.update');
});

Route::prefix('transfers')->middleware(['jwt.web:transfers'])->group(function () {
    Route::get('/', [\App\Http\Controllers\Api\TransferController::class, 'index'])->name('transfers.index');
    Route::post('/', [\App\Http\Controllers\Api\TransferController::class, 'store'])->name('transfers.store');
    Route::get('/{transfer}', [\App\Http\Controllers\Api\TransferController::class, 'show'])->name('transfers.show');
    Route::delete('/{transfer}', [\App\Http\Controllers\Api\TransferController::class, 'destroy'])->name('transfers.destroy');
});

Route::prefix('refund')->middleware(['jwt.web:refund'])->group(function () {
    Route::post('/', [\App\Http\Controllers\Api\RefundController::class, 'store'])->name('refund.store');
});

Route::prefix('balance')->middleware(['jwt.web:balance'])->group(function () {
    Route::get('/', [\App\Http\Controllers\Api\BalanceController::class, 'get'])->name('balance.get');
});

Route::prefix('bank-account')->middleware(['jwt.web:bank'])->group(function () {
    Route::get('/', [\App\Http\Controllers\Api\BankController::class, 'get'])->name('bank-account.get');
    Route::post('/', [\App\Http\Controllers\Api\BankController::class, 'store'])->name('bank-account.store');
    Route::put('/', [\App\Http\Controllers\Api\BankController::class, 'update'])->name('bank-account.update');
});

Route::prefix('recipients')->middleware(['jwt.web:recipient'])->group(function () {
    Route::post('/', [\App\Http\Controllers\Api\RecipientController::class, 'store'])->name('recipients.store');
    Route::get('/plans/{plan_id}', [\App\Http\Controllers\Api\RecipientsPlanController::class, 'index'])->name('recipients.plan.index');
});

//API Checkout
Route::group(['prefix' => 'checkout', 'middleware' => 'audit.route'],
    function () {
        //webhook confirm payment
        Route::post('order/paid', [\App\Http\Controllers\Webhooks\PaidMundipaggController::class, 'boletoPaid'])
            ->name('mundipagg.order.paid');

        Route::post('order/paid/pagarme', [\App\Http\Controllers\Webhooks\StatusPagarmeController::class, 'transactionStatus'])
            ->name('pagarme.transaction.status');

        Route::post('order/refund/mundipagg', [\App\Http\Controllers\Webhooks\RefundMundipaggController::class, 'boletoRefunded'])
            ->name('mundipagg.order.refund');

        Route::post('recipient/update/mundipagg', [\App\Http\Controllers\Webhooks\RecipientStatusMundipaggController::class, 'recipientUpdated'])
            ->name('mundipagg.recipient.update');

        //Dowload boleto
        Route::get('boleto/{order_id}', ['as' => 'checkout.boleto.download', 'uses' => 'CheckoutApiController@downloadBoleto']);
        Route::group(['middleware' => 'api.checkout.access'], function () {
            Route::post('installmentvalue', ['as' => 'checkout.installmentvalue', 'uses' => 'CheckoutApiController@getInstallmentValue']);
            Route::get('platforms', ['as' => 'checkout.platform.list', 'uses' => 'CheckoutApiController@listPlatforms']);
            Route::get('platforms/{platform_id}', ['as' => 'checkout.platform.get', 'uses' => 'CheckoutApiController@getPlatform']);
            Route::get('platforms/{platform_id}/plans', ['as' => 'checkout.plan.list', 'uses' => 'CheckoutApiController@listPlans']);

            Route::get('platforms/{platform_id}/plans/{plan_id}', [
                \App\Http\Controllers\CheckoutApiController::class, 'getPlan'
            ])->name('checkout.plan.get');

            Route::post('lead', [\App\Http\Controllers\CheckoutApiController::class, 'lead'])->name('checkout.lead.save');

            //Recebe obter id do usuário por middleware da mesma forma que api.checkout porém sem contar o número de tentativas
            Route::get('checkplan/{plan_id}', ['as' => 'mundipagg.checkout.checkPlan', 'uses' => 'CheckoutApiController@checkPlan']);

            Route::get('platforms/{platform_id}/plans/{plan_id}/hash/{hash}', [
                \App\Http\Controllers\OneClickBuyController::class, 'info'
            ])->name('checkout.oneclick.get');

            Route::middleware([\App\Http\Middleware\CheckoutGoogleRecaptchaV3::class])->group(function () {
                Route::post('subscriber', ['as' => 'checkout.subscriber.save', 'uses' => 'CheckoutApiController@saveSubscriber']);
                Route::get('cupom/{cupom_code}', ['as' => 'mundipagg.checkout.cupom', 'uses' => 'CheckoutApiController@checkCupom']);

                Route::middleware([
                    'api.checkout',
                    \App\Http\Middleware\RequiresClientVerified::class,
                    \App\Http\Middleware\LogCheckoutRequestMiddleware::class
                ])->group(function () {
                    Route::post('upsell/{platform_id}', ['as' => 'mundipagg.checkout.upsell', 'uses' => 'CheckoutApiController@upSell']);
                    Route::post('{platform_id}/{plan_id}', ['as' => 'mundipagg.checkout.save', 'uses' => 'CheckoutApiController@checkout']);
                });

                Route::post('{platform_id}/{plan_id}/{hash}', [
                    \App\Http\Controllers\OneClickBuyController::class, 'buy'
                ])->name('checkout.oneclick.buy');

            });

            Route::middleware([])->group(function () {
                // Affiliation routes

                Route::get('platforms/{platform_id}/plans/{plan_id}/affiliation/settings', [
                    \App\Http\Controllers\Affiliation\AffiliationController::class, 'settings'
                ])->name('checkout.affiliation.settings');

                Route::put('platforms/{platform_id}/plans/{plan_id}/affiliation/settings', [
                    \App\Http\Controllers\Affiliation\AffiliationController::class, 'update'
                ])->name('checkout.affiliation.settings.update');

                Route::get('platforms/{platform_id}/plans/{plan_id}/affiliates', [
                    \App\Http\Controllers\Affiliation\AffiliateController::class, 'index'
                ])->name('checkout.affiliate.index');

                Route::post('platforms/{platform_id}/plans/{plan_id}/affiliates', [
                    \App\Http\Controllers\Affiliation\AffiliateController::class, 'store'
                ])->name('checkout.affiliate.store');
            });
        });
    });

if (!app()->environment('production')) {
    Route::prefix('test')->group(function () {
        Route::post('/subscription', [\App\Http\Controllers\Test\ChargeRulerController::class, 'subscription']);
        Route::post('/no-limit', [\App\Http\Controllers\Test\ChargeRulerController::class, 'noLimit']);
    });
}

Route::post('/maintenance/payment/{payment_id}', [\App\Http\Controllers\Maintenance\PaymentStatusController::class, 'changeStatus']);
