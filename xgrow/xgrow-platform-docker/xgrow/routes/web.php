<?php

use App\Http\Controllers\Api\AffiliatesController;
use App\Http\Controllers\Api\AffiliationsController;
use App\Http\Controllers\Api\AuthorController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\DeveloperController;
use App\Http\Controllers\Api\DocumentsController;
use App\Http\Controllers\Api\FinancialController;
use App\Http\Controllers\Api\LearningAreaController;
use App\Http\Controllers\Api\ResourcesController;
use App\Http\Controllers\BankDataController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Reports\SalesReportController;
use App\Http\Controllers\Subscriber\SubscriberBlockedsController;
use App\Http\Controllers\Subscriber\SubscriberDataController;
use App\Http\Controllers\Subscriber\SubscriberEmailsController;
use App\Http\Controllers\Subscriber\SubscriberListController;
use App\Http\Controllers\Subscriber\SubscriberPaymentsController;
use App\Http\Controllers\Subscriber\SubscriptionProductsController;
use App\Http\Controllers\SubscriberController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

// Login Routes...

Route::get('login', ['as' => 'login', 'uses' => 'Auth\LoginController@showLoginForm']);
Route::post('login', ['as' => 'login.post', 'uses' => 'Auth\LoginController@login']);
Route::post('logout', ['as' => 'logout', 'uses' => 'Auth\LoginController@logout']);
Route::get('logout', 'Auth\LoginController@logout');

Route::get('register', 'PlatformUserController@registerNewClient')->name('get.register');
//Route::get('check-email-register/{email}', 'PlatformUserController@checkEmailBeforeRegistering')->middleware('throttle:5,2')->name('check.email.register');
//Route::post('register', 'PlatformUserController@register')->middleware('throttle:10,10')->name('post.register');
Route::get('check-email-register/{email}', 'PlatformUserController@checkEmailBeforeRegistering')->name('check.email.register');
Route::post('register', 'PlatformUserController@register')->name('post.register');

Route::get('confirm-access', ['as' => 'confirm.access.email', 'uses' => 'SubscriberNotificationController@confirmAccessedCourseEmail']);

// Password Reset Routes...
Route::group(['prefix' => 'password'], function () {
    Route::get('reset', ['as' => 'password.reset', 'uses' => 'Auth\ForgotPasswordController@showLinkRequestForm']);
    Route::middleware(['preventRefererInjection'])->group(function () {
        Route::post('email', ['as' => 'password.email', 'uses' => 'Auth\ForgotPasswordController@sendResetLinkEmail']);
    });
    Route::get('reset/{token}', ['as' => 'password.reset.token', 'uses' => 'Auth\ResetPasswordController@showResetForm']);
    Route::post('request', ['as' => 'password.request', 'uses' => 'Auth\ResetPasswordController@reset']);
});

Route::group(
    ['prefix' => 'hotmart'],
    function () {
        Route::post('{id}/purchases', ['uses' => 'HotmartController@purchases']);
        Route::post('{id}/unsubscribe', ['uses' => 'HotmartController@unsubscribe']);
        Route::get('{id}/imports/{arquivo}', ['uses' => 'HotmartController@imports']);
    }
);


Route::group(['prefix' => 'subscribers'], function () {
    Route::group(['prefix' => 'password'], function () {
        Route::get('resetemail/{user_type}', ['as' => 'subscribers.password.reset', 'uses' => 'Auth\ForgotPasswordController@showLinkRequestForm']);
        Route::post('email', ['as' => 'subscribers.password.email', 'uses' => 'Auth\ForgotPasswordController@sendResetLinkEmail']);
        Route::get('reset/{token}', ['as' => 'subscribers.password.reset.token', 'uses' => 'Auth\ResetPasswordController@showResetForm']);
        Route::post('request', ['as' => 'subscribers.password.request', 'uses' => 'Auth\ResetPasswordController@reset']);
    });
});


Route::post('reset_password_without_token', 'PlatformUserController@validatePasswordRequest');

//fora de auth
Route::get('file/download/{filename?}', ['as' => 'file.download', 'uses' => 'FileController@download']);

// Terms Routes
Route::get('glossary', 'TermsController@glossary');
Route::get('privacy_policy', 'TermsController@privacyPolicy');
Route::get('xgrow_terms', 'TermsController@xgrowTerms');

Route::get('verify/resend', 'Auth\TwoFactorController@resend')->name('verify.resend');
Route::resource('verify', 'Auth\TwoFactorController')->only(['index', 'store']);

Route::get('affiliate-invite', ['as' => 'affiliate.invite', 'uses' => 'AffiliateInviteController@affiliateInvite'])->middleware('auth');
Route::post('affiliation-confirm', ['as' => 'affiliation.confirm', 'uses' => 'AffiliateInviteController@affiliationConfirm'])->middleware('auth');

/* Product Links */
Route::group(['prefix' => 'product-links', 'middleware' => 'auth'], function () {
    Route::get('list/{product_id}', 'ProductsLinksController@list')->name('product.links.list');
    Route::get('list-plans/{product_id}', 'ProductsLinksController@listPlans')->name('product.links.list.plans');
    Route::post('create', 'ProductsLinksController@create')->name('product.links.create');
    Route::put('update/{id}', 'ProductsLinksController@update')->name('product.links.update');
    Route::delete('delete/{id}', 'ProductsLinksController@delete')->name('product.links.delete');
});

Route::group(
    ['middleware' => ['auth2f', 'audit.route', 'verify.ip']],
    function () {
        Route::get('/', 'DashboardController@index')->name('home-one');
        Route::get('/home', 'DashboardController@index')->name('home-two');
        Route::get('/', 'DashboardController@index')->name('home');
        Route::get('/user-info', 'DashboardController@userInfo')->name('user.info');

        Route::prefix('comments')->group(function () {
            Route::get('', ['as' => 'comments.index', 'uses' => 'CommentsController@index']);
            Route::get('add_comment_id', ['as' => 'comments.add_comment_id', 'uses' => 'CommentsController@addCommentId']);
            Route::get('subscribers', ['as' => 'comments.subscribers', 'uses' => 'CommentsController@subscribers']);
            Route::get('manage', ['as' => 'comments.manage', 'uses' => 'CommentsController@manage']);
            Route::get('pedding', ['as' => 'comments.pedding', 'uses' => 'CommentsController@pedding']);
            Route::delete('', ['as' => 'comments.destroy', 'uses' => 'CommentsController@destroy']);
            Route::patch('approved', ['as' => 'comments.approved', 'uses' => 'CommentsController@approved']);
            Route::post('send_comment', ['as' => 'comments.send_comment', 'uses' => 'CommentsController@sendComment']);
            Route::post('change_status_selected', ['as' => 'comments.change_status_selected', 'uses' => 'CommentsController@changeStatusSelected']);
            Route::post('delete_selected', ['as' => 'comments.delete_selected', 'uses' => 'CommentsController@deleteSelected']);
            Route::patch('set-approve-comments', ['as' => 'comments.set_approve_comments', 'uses' => 'CommentsController@setApproveComments']);
        });

        Route::middleware('role:product')->prefix('products')->group(function () {
            Route::get('', ['as' => 'products.index', 'uses' => 'ProductController@index']);
            Route::get('/next', [ProductController::class, 'indexNext']);
            Route::get('/get-all-products', [ProductController::class, 'getAllProducts'])->name('products.get-all');
            Route::get('create', ['as' => 'products.create', 'uses' => 'ProductController@create']);
            Route::get('plan/{id}', ['as' => 'products.plan', 'uses' => 'ProductController@productPlan']);
            Route::post('product-store}', ['as' => 'products.store', 'uses' => 'ProductController@store']);
            Route::put('product-store-plan/{id}', ['as' => 'products.store.plan', 'uses' => 'ProductController@storePlan']);
            Route::get('delivery/{id}', ['as' => 'products.delivery', 'uses' => 'ProductController@productDelivery']);
            Route::post('attach-content-to-product', ['as' => 'products.attach.content', 'uses' => 'ProductController@attachCourseOrSectionToProduct']);
            Route::post('detach-content-to-product', ['as' => 'products.detach.content', 'uses' => 'ProductController@detachCourseOrSectionToProduct']);
            //            Route::get('total-subscribers', 'ProductController@totalSubscribersByProduct')->name('products.total.subscribers');
            Route::post('unlimited-delivery', ['as' => 'products.unlimited.delivery', 'uses' => 'ProductController@unlimitedDelivery']);
            Route::get('info/{id}', ['as' => 'products.info', 'uses' => 'ProductController@productInfo']);

            Route::get('list', [ProductController::class, 'list'])->name('products.list');

            //            Route::delete('delete{id}', ['as' => 'products.destroy', 'uses' => 'ProductController@destroy']);
            Route::delete('delete-resource', ['as' => 'products.delete.resources', 'uses' => 'ProductController@deleteResource']);
            Route::delete('delete-product/{id}', 'ProductController@deleteProduct')->name('products.delete');
            Route::delete('delete-plan/{id}', 'ProductController@destroyPlan')->name('products.plans.destroy');

            Route::get('get-resources/{id}/{type}', ['as' => 'products.get.resources', 'uses' => 'ProductController@getOrderBumpsAndUpSell']);
            Route::get('get-resource/{id}', ['as' => 'products.get.one.resource', 'uses' => 'ProductController@getResource']);
            Route::post('resources', ['as' => 'products.save.resources', 'uses' => 'ProductController@saveResource']);
            Route::post('update-resources', 'ProductController@updateResource')->name('products.update.resources');
            Route::put('update-affiliation-enabled/{id}', 'ProductController@updateAffiliationEnabled')->name('products.update.affiliation.enabled');
            Route::post('update-image-resources', 'ProductController@updateImageResource')->name('products.image.resources');
            Route::put('{id}/status', 'ProductController@statusProduct')->name('products.update.status');
            Route::put('{id}/status-plan', 'ProductController@statusPlan')->name('products.update.status.plan');
            Route::post('{plan}/replicate', 'ProductController@replicateProduct')->name('products.replicate');

            Route::get('gateway', 'PlanController@getGateway')->name('plans.gateway');
            Route::get('verify-gateway', 'PlanController@verifyGateway')->name('plans.verify-gateway');

            // Deliveries
            Route::post('set-delivery', 'ProductController@setDelivery')->name('products.set.delivery');

            // 2nd Step Edit
            Route::post('favorite-plan', 'ProductController@favoritePlan')->name('products.favorite.plan');
            Route::get('get-all', 'ProductController@getProducts')->name('products.get.all');
            //            Route::get('list-products', 'ProductController@getListProducts')->name('products.list');
            Route::get('new-plan/{id}', 'ProductController@createPlanByProduct')->name('products.new.plan');
            Route::get('new-plan-product/{id}', 'ProductController@isolatedProductPlan')->name('products.new.plan.product');
            Route::get('edit-plan-product/{id}', 'ProductController@isolatedProductPlan')->name('products.edit.plan.product');
            Route::put('new-plan-product/{id}', 'ProductController@isolatedStorePlan')->name('products.post.new.plan');
            Route::patch('plan-allow-change/{plan_id}', 'ProductController@planAllowChange')->name('plan.allow.change');

            Route::prefix('{id}')->group(
                function () {
                    Route::get('list-plans', ['as' => 'list.plans', 'uses' => 'ProductController@listPlans']);
                    Route::get('edit', ['as' => 'products.edit', 'uses' => 'ProductController@edit']);
                    Route::get('plans', ['as' => 'products.edit-plan', 'uses' => 'ProductController@editPlan']);
                    Route::get('list-links', ['as' => 'products.list.link', 'uses' => 'ProductController@listCheckoutLinks']);
                    //Route::get('product-page', ['as' => 'products.product.page', 'uses' => 'ProductController@productPage']);
                    Route::post('config', ['as' => 'products.page.config', 'uses' => 'ProductController@config']);
                }
            );

            Route::group(['prefix' => 'deliveries'], function () {
                Route::post('', 'ProductController@getDelivery')->name('products.get.all.deliveries');
                Route::post('attach', [ProductController::class, 'attachContentOnProduct'])->name('products.attach.content.graphql');
                Route::post('detach', [ProductController::class, 'detachContentOnProduct'])->name('products.detach.content.graphql');
                Route::post('list', [ProductController::class, 'contentAttachedOnProduct'])->name('products.list.content.graphql');
                Route::post('clear-cache', [ProductController::class, 'clearDeliveryCache'])->name('products.subscriber.clear.cache');
            });
        });

        //TODO Dont remove yet changed by products
        // Route::prefix('plans')->group(function () {
        //     Route::get('list', ['as' => 'plans.list', 'uses' => 'PlanController@list']);
        // });

        Route::middleware('role:lead')->group(function () {
            Route::prefix('leads')->group(function () {
                Route::get('', ['as' => 'leads.index', 'uses' => 'LeadController@index']);
                Route::get('get-leads', ['as' => 'get.leads', 'uses' => 'LeadController@searchLeads']);
                Route::get('get-lead-fail-details/{id}', 'LeadController@getPaymentStatus')->name('get.lead.fail.detail');
            });
        });


        //compartilhado com as roles lead e subscriber
        Route::prefix('subscribers')->group(function () {
            Route::get('{id}/edit/next', [SubscriberController::class, 'editNext'])->name('subscribers.editNext');
            Route::get('{id}/edit', ['as' => 'subscribers.edit', 'uses' => 'SubscriberController@edit']);
            Route::put('{id}', ['as' => 'subscribers.update', 'uses' => 'SubscriberController@update']);
        });

        Route::prefix('subscribers')->middleware('role:import-suscriber')->group(function () {
            Route::post('import-sub', ['as' => 'subscribers.import', 'uses' => 'SubscriberController@import']);
            Route::get('import', ['as' => 'subscribers.import.create', 'uses' => 'SubscriberController@importCreate']);

            Route::get('import/next', [SubscriberController::class, 'importCreateNext'])->name('subscribers.importNext');
        });

        Route::middleware('role:subscriber')->group(function () {
            Route::patch('{subscriber_id}', [SubscriberDataController::class, 'update'])
                ->name('subscribers.next.update');
            Route::prefix('subscribers')->name('subscribers.')->group(function () {
                Route::prefix('next')->name('next.')->group(function () {
                    Route::get('/subscriber-user', [SubscriberListController::class, 'searchSubscriber'])
                        ->name('user.index');

                    Route::post('/subscriber-user', [SubscriberListController::class, 'storeSubscriber'])
                        ->name('store');

                    Route::post('{id}/resend-data', [SubscriberListController::class, 'resendData'])
                        ->name('resend-data');

                    Route::delete('{subscriber_id}', [SubscriberListController::class, 'destroy'])
                        ->name('destroy');



                    Route::get('/blocked-subscriber-user', [SubscriberBlockedsController::class, 'searchBlockedSubscriber'])
                        ->name('blocked.user.index');

                    Route::put('/blocked-subscriber-user', [SubscriberBlockedsController::class, 'updateBlockedSubscriber'])
                        ->name('blocked.user.update');

                    Route::get('{subscriber_id}', [SubscriberDataController::class, 'show'])
                        ->name('show');

                    Route::patch('{subscriber_id}', [SubscriberDataController::class, 'update'])
                        ->name('resend-data');

                    Route::get('/list-subscriber-payments/{subscriberId}', [SubscriberPaymentsController::class, 'listSubscriberPayments'])
                        ->name('subscriber.payments.index');

                    Route::post('/payments/refund', [SubscriberPaymentsController::class, 'refund'])
                        ->name('payments.refund');

                    Route::get('{subscriberId}/resend-access-data', [SubscriberEmailsController::class, 'resendData'])
                        ->name('resend.access.data');

                    /**
                     * Novas rotas para Edição de alunos: Aba produtos XPP-550
                     */
                    Route::put('subscriptions/change-product', [SubscriptionProductsController::class, 'changeSubscriptionStatus'])
                        ->name('subscription.change.product');

                    Route::put('subscriptions/{subscriptionId}/status', [SubscriptionProductsController::class, 'cancelNotRefund'])
                        ->name('subscriptions.cancel.not-refund');

                    Route::post('subscriptions/refund', [CheckoutController::class, 'refund'])
                        ->name('subscriptions.refund');

                    Route::get('subscriptions/{paymentId}/send-purchase-proof', [SubscriptionProductsController::class, 'sendPurchaseProof'])->name('subscriptions.send.buyed.proof');

                    Route::get('subscriptions/{paymentId}/send-bank-slip', [SubscriptionProductsController::class, 'sendBankSlip'])->name('subscriptions.resend.boleto');

                    Route::get('subscriptions/{paymentPlanId}/send-refund', [SubscriptionProductsController::class, 'sendRefund'])->name('subscriptions.send.refund');

                    Route::get('subscriptions/{paymentPlanId}/refund-proof', [SubscriptionProductsController::class, 'refundProof'])->name('subscriptions.refund.proof');

                    Route::get('subscriptions/{subscriberId}/products', [SubscriptionProductsController::class, 'listProductsBySubscriber'])->name('subscriptions.products');

                    Route::get('subscriptions/{subscriberId}/plans', [SubscriptionProductsController::class, 'listPlansBySubscriber'])->name('subscriptions.plans');
                });
            });

            Route::group(
                ['prefix' => 'subscribers'],
                function () {
                    Route::get('/next', [SubscriberController::class, 'indexNext'])->name('subscribers.indexNext');

                    Route::get('subscriber-user', ['as' => 'subscribers.user.index', 'uses' => 'SubscriberController@searchSubscriber']);
                    Route::get('blocked-subscriber-user', ['as' => 'subscribers.blocked.user.index', 'uses' => 'SubscriberController@searchBlockedSubscriber']);
                    Route::post('update-blocked-subscriber-user', ['as' => 'subscribers.blocked.user.update', 'uses' => 'SubscriberController@updateBlockedSubscriber']);
                    Route::get('', ['as' => 'subscribers.index', 'uses' => 'SubscriberController@index']);
                    Route::get('create', ['as' => 'subscribers.create', 'uses' => 'SubscriberController@create']);
                    Route::post('', ['as' => 'subscribers.store', 'uses' => 'SubscriberController@store']);

                    Route::put('{id}/status', ['as' => 'subscribers.update_status', 'uses' => 'SubscriberController@status']);
                    Route::post('delete', ['as' => 'subscribers.destroy', 'uses' => 'SubscriberController@destroy']);
                    Route::get('{id}/resend-date', ['as' => 'subscribers.resend-data', 'uses' => 'SubscriberController@resendData']);
                    Route::get('export', ['as' => 'subscribers.export.create', 'uses' => 'SubscriberController@exportCreate']);
                    Route::post('export-sub', ['as' => 'subscribers.export', 'uses' => 'SubscriberController@export']);

                    //Atualizar dados do cartão de crédito
                    Route::get('{subscriber_id}/creditcard', ['as' => 'subscribers.creditcard.list', 'uses' => 'Mundipagg\CreditCardController@listCreditCards']);
                    Route::get('{subscriber_id}/creditcard/{id}', ['as' => 'subscribers.creditcard.get', 'uses' => 'Mundipagg\CreditCardController@getSubscriberCreditCard']);
                    Route::post('{subscriber_id}/creditcard', ['as' => 'subscribers.creditcard.store', 'uses' => 'Mundipagg\CreditCardController@storeSubscriberCreditCard']);
                    Route::post('{subscriber_id}/creditcard/{id}', ['as' => 'subscribers.creditcard.change-default', 'uses' => 'Mundipagg\CreditCardController@changeDefaultSubscriberCreditCard']);
                    Route::post('{subscriber_id}/creditcard/{id}/delete', ['as' => 'subscribers.creditcard.destroy', 'uses' => 'Mundipagg\CreditCardController@deleteSubscriberCreditCard']);

                    // Enviar link de troca de cartão
                    Route::get('{subscriber_id}/change_card', ['as' => 'subscribers.change_card_link', 'uses' => 'SubscriberController@sendChangeCardLink']);
                    Route::get('{subscriber_id}/get-change-card-link', ['as' => 'subscribers.get_change_card_link', 'uses' => 'SubscriberController@getChangeCardLink']);


                    Route::prefix('notifications')->group(function () {
                        Route::get('not-access-course', ['as' => 'subscribers.notification.not-access-course', 'uses' => 'SubscriberNotificationController@sendNotAccessedCourseEmail']);
                        Route::post('send-email-for-never-access-course', 'SubscriberNotificationController@sendNotAccessedCourseEmailFull')->name('subscribers.notification.never-access-course');
                    });
                }
            );


            Route::group(['prefix' => 'subscriptions'], function () {
                Route::post('cancel', ['as' => 'subscriptions.cancel', 'uses' => 'SubscriptionController@cancel']);
                Route::put('/{subscription}/status', [
                    'as' => 'subscriptions.cancel.not-refund',
                    'uses' => 'SubscriptionController@cancelNotRefund'
                ]);

                // @deprecated
                Route::delete('/{subscription}/payments', ['as' => 'subscriptions.cancel.refund', 'uses' => 'SubscriptionController@cancelRefund']);

                // @deprecated
                Route::delete('/{subscription}/payments-pix', 'SubscriptionController@cancelRefundPix')
                    ->name('subscriptions.cancel.refund-pix');

                // @deprecated
                Route::delete('/{subscription}/payments-boleto', 'SubscriptionController@cancelRefundBoleto')
                    ->name('subscriptions.cancel.refund-boleto');

                Route::delete('/order_number/{order_number}', [SubscriptionController::class, 'cancelOrderNumber'])->name('subscriptions.cancel.order_number');
            });
        });

        Route::prefix('coupons')->name('coupons.')->middleware('role:coupons')->group(
            function () {
                Route::get('/next', ['as' => 'indexNext', 'uses' => 'CouponController@indexNext']);
                Route::get('/get-all-coupons', ['as' => 'get-all', 'uses' => 'CouponController@getCoupons']);
                Route::get('/get-all-plans', ['as' => 'get-all-plans', 'uses' => 'CouponController@getPlans']);

                Route::get('/', ['as' => 'index', 'uses' => 'CouponController@index']);
                Route::post('/verify', 'CouponController@verify')->name('verify');
                Route::get('/datatables', ['as' => 'index.datatables', 'uses' => 'CouponController@couponsData']);
                Route::get('/create', ['as' => 'create', 'uses' => 'CouponController@create']);
                Route::post('/', ['as' => 'store', 'uses' => 'CouponController@store']);
                Route::get('/{id}/edit', ['as' => 'edit', 'uses' => 'CouponController@edit']);
                Route::put('/{id}', ['as' => 'update', 'uses' => 'CouponController@update']);
                Route::delete('/{id}', ['as' => 'destroy', 'uses' => 'CouponController@destroy']);
                Route::get('/{coupon}/mailings/datatables', ['as' => 'mailings.index.datatables', 'uses' => 'CouponController@mailingData']);
                Route::post('/{coupon}/mailings', ['as' => 'mailings.store', 'uses' => 'CouponController@storeMailing']);
                Route::post('/{coupon}/mailings/upload', ['as' => 'mailings.upload', 'uses' => 'CouponController@storeMailingFromFile']);
                Route::post('/{coupon}/mailings/{mailing}', ['as' => 'mailings.resend', 'uses' => 'CouponController@resendMail']);
                Route::delete('/{coupon}/mailings/{mailing}', ['as' => 'mailings.destroy', 'uses' => 'CouponController@destroyMail']);
            }
        );

        Route::group(
            ['prefix' => 'gallery'],
            function () {
                Route::post('images', ['as' => 'gallery.images', 'uses' => 'GalleryController@images']);
            }
        );

        Route::group(
            ['prefix' => 'user'],
            function () {
                Route::get('', ['as' => 'user.index', 'uses' => 'PlatformUserController@index']);
                Route::get('support', ['as' => 'user.support', 'uses' => 'PlatformUserController@support']);
                Route::post('store', ['as' => 'user.store', 'uses' => 'PlatformUserController@store']);
                Route::put('update', ['as' => 'user.update', 'uses' => 'PlatformUserController@update']);
                Route::middleware('throttle:3,5')->group(function () {
                    Route::post('email-support', ['as' => 'user.email-support', 'uses' => 'PlatformUserController@emailSupport']);
                });
            }
        );

        Route::prefix('integracao')->middleware('role:integration')->group(
            function () {
                Route::get('', ['as' => 'integracao.index', 'uses' => 'IntegracaoController@index']);
                Route::get('create', ['as' => 'integracao.create', 'uses' => 'IntegracaoController@create']);
                Route::get('edit/{id}', ['as' => 'integracao.edit', 'uses' => 'IntegracaoController@edit']);
                Route::put('{id}/status', ['as' => 'integracao.update_status', 'uses' => 'IntegracaoController@status']);
                Route::put('update/{id}', ['as' => 'integracao.update', 'uses' => 'IntegracaoController@update']);
                Route::put('store', ['as' => 'integracao.store', 'uses' => 'IntegracaoController@store']);
                Route::delete('destroy/{id}', ['as' => 'integracao.destroy', 'uses' => 'IntegracaoController@destroy']);
                Route::get('activecampaign/lists', ['as' => 'integracao.activecampaign.lists', 'uses' => 'IntegracaoController@getActiveCampaignLists']);
                Route::get('activecampaign/tags', ['as' => 'integracao.activecampaign.tags', 'uses' => 'IntegracaoController@getActiveCampaignTags']);
                //            Route::get('payments', ['as' => 'integracao.payments', 'uses' => 'Getnet\SubscriptionController@payments']);
                //            Route::get('get-payments-data', ['as' => 'datatables.payments', 'uses' => 'Getnet\SubscriptionController@paymentsData']);
                Route::get('edit-payment/{id}', ['as' => 'integracao.edit-payment', 'uses' => 'Getnet\SubscriptionController@editPayment']);
                //                Route::get('teste', ['as' => 'integracao.teste', 'uses' => 'IntegracaoController@teste']);
                //                Route::post('teste', ['as' => 'integracao.teste.post', 'uses' => 'IntegracaoController@teste']);
            }
        );

        Route::group(
            ['prefix' => 'integracao-logs'],
            function () {
                Route::get('', ['as' => 'integracao-logs.index', 'uses' => 'IntegracaoController@logs']);
                Route::get('errors', ['as' => 'integracao-logs.errors', 'uses' => 'IntegracaoController@logsErrors']);
                Route::get('details/{id}', ['as' => 'integracao-logs.details', 'uses' => 'IntegracaoController@logsDetails']);
                Route::get('details-error/{id}', ['as' => 'integracao-logs.detailsError', 'uses' => 'IntegracaoController@logsDetailsErrors']);
            }
        );

        Route::group(
            ['prefix' => 'integracaoAction'],
            function () {
                Route::get('', ['as' => 'integracaoAction.index', 'uses' => 'IntegracaoActionController@index']);
                Route::put('create/{id}', ['as' => 'integracaoAction.create', 'uses' => 'IntegracaoActionController@create']);
                Route::delete('destroy/{id}/{webhookId}', ['as' => 'integracaoAction.destroy', 'uses' => 'IntegracaoActionController@destroy']);
                Route::put('updateStatus/{id}/{webhookId}', ['as' => 'integracaoAction.updateStatus', 'uses' => 'IntegracaoActionController@updateStatus']);
            }
        );

        Route::group(
            ['prefix' => 'dashboard'],
            function () {
                Route::get('', ['as' => 'dashboard.index', 'uses' => 'DashboardController@index']);
            }
        );

        Route::prefix('comments')->middleware('role:comment')->group(function () {
            Route::get('', ['as' => 'comments.index', 'uses' => 'CommentsController@index']);
            Route::get('manage', ['as' => 'comments.manage', 'uses' => 'CommentsController@manage']);
            Route::get('pedding', ['as' => 'comments.pedding', 'uses' => 'CommentsController@pedding']);
        });


        // TODO remover após adicionar no novo fluxo da LA /course/ID/experience
        Route::middleware('role:course')->group(
            function () {
                Route::prefix('course/{id}')->group(function () {
                    Route::prefix('experience')->group(function () {
                        Route::get('', 'Api\CourseExperienceController@experience')->name('course.experience');
                        Route::get('get-modules', 'Api\CourseExperienceController@getModules')->name('course.experience.get.modules');
                        Route::post('module', 'Api\CourseExperienceController@saveModule')->name('course.experience.post.module');
                        Route::get('get-contents', 'Api\CourseExperienceController@getContents')->name('course.experience.get.contents');
                        Route::post('content', 'Api\CourseExperienceController@storeContent')->name('course.experience.post.content');
                        Route::delete('content', 'Api\CourseExperienceController@deleteContent')->name('course.experience.delete.content');
                        Route::get('get-authors', 'Api\CourseExperienceController@getAuthors')->name('course.experience.get.authors');
                        Route::post('create-author', 'Api\CourseExperienceController@createAuthor')->name('course.experience.create.author');
                        Route::post('sync', 'Api\CourseExperienceController@syncDiagram')->name('course.experience.sync');
                    });
                });
            }
        );

        Route::prefix('video-upload')->group(function () {
            Route::post('send', ['as' => 'video-upload.send', 'uses' => 'VideoUploadController@send']);
            Route::get('get-data-upload', ['as' => 'video-upload.get-data-upload', 'uses' => 'VideoUploadController@getDataUpload']);
            Route::post('get-video', ['as' => 'video-upload.get-data-upload', 'uses' => 'VideoUploadController@getVideo']);
        });

        Route::prefix('forum')->middleware('role:forum')->group(function () {
            Route::get('', ['as' => 'forum.index', 'uses' => 'ForumController@index']);
            Route::post('store', ['as' => 'forum.store', 'uses' => 'ForumController@store']);
            Route::get('active', 'ForumController@isActive')->name('forum.active');

            Route::prefix('topics')->group(function () {
                Route::get('', ['as' => 'topic.create', 'uses' => 'ForumTopicController@create']);
                Route::post('store', ['as' => 'topic.store', 'uses' => 'ForumTopicController@store']);
                Route::get('edit/{id}', ['as' => 'topic.edit', 'uses' => 'ForumTopicController@edit']);
                Route::post('update/{id}', ['as' => 'topic.update', 'uses' => 'ForumTopicController@update']);
                Route::post('get-topics', ['as' => 'api.forum.topic', 'uses' => 'ForumTopicController@getTopics']);
            });

            Route::prefix('moderation')->group(function () {
                Route::get('', ['as' => 'forum.moderation', 'uses' => 'ForumPostController@postModerationAccepted']);
                Route::get('pending', ['as' => 'forum.moderation.pending', 'uses' => 'ForumPostController@postModerationPending']);
            });
        });

        Route::prefix('file')->group(function () {
            Route::post('delete', ['as' => 'file.delete', 'uses' => 'FileController@delete']);
        });

        Route::group(
            ['prefix' => 'templates'],
            function () {

                Route::get('three-columns', function () {
                    $data['sessionTitle'] = config('constantsTemp.sessionTitle');
                    $data['cards'] = config('constantsTemp.cards');

                    return view('themes.three-columns.index', $data);
                });

                Route::get('lateral-description', function () {
                    $data['sessionTitle'] = config('constantsTemp.sessionTitle');
                    $data['contentTitle'] = config('constantsTemp.contentTitle');
                    $data['apperance'] = config('constantsTemp.apperance');
                    $data['cards'] = config('constantsTemp.cards');

                    return view('themes.lateral-description.index', $data);
                });

                Route::get('horizontal-highlight', function () {
                    $data['sessionTitle'] = config('constantsTemp.sessionTitle');
                    $data['contentTitle'] = config('constantsTemp.contentTitle');
                    $data['apperance'] = config('constantsTemp.apperance');
                    $data['cards'] = config('constantsTemp.cards');
                    $data['highlight'] = config('constantsTemp.highlight'); // nesse ponto será uma consulta com where

                    return view('themes.horizontal-highlight.index', $data);
                });
            }
        );

        Route::middleware('role:permission')->group(function () {
            Route::get('permission/get-users', ['as' => 'permission.get_users', 'uses' => 'PermissionController@getUsers']);
            Route::resource('permission', 'PermissionController');
        });

        Route::middleware('role:user')->prefix('platform-config/users')->group(
            function () {
                Route::get('', ['as' => 'platforms-users.index', 'uses' => 'PlatformSiteConfigController@Userindex']);
                Route::get('/next', ['as' => 'platforms-users.index.next', 'uses' => 'PlatformSiteConfigController@UserIndexNext']);
                Route::get('/list', ['as' => 'platforms-users.get.users', 'uses' => 'PlatformSiteConfigController@GetUsers']);

                Route::post('verify', ['as' => 'platforms-users.verify', 'uses' => 'PlatformSiteConfigController@UserVerify']);
                Route::get('create', ['as' => 'platforms-users.create', 'uses' => 'PlatformSiteConfigController@UserCreate']);
                Route::post('store', ['as' => 'platforms-users.store', 'uses' => 'PlatformSiteConfigController@UserStore']);
                Route::get('{id}/edit', ['as' => 'platforms-users.edit', 'uses' => 'PlatformSiteConfigController@UserEdit']);

                Route::put('{id}', ['as' => 'platforms-users.update', 'uses' => 'PlatformSiteConfigController@UserUpdate']);

                Route::delete('{id}', ['as' => 'platforms-users.destroy', 'uses' => 'PlatformSiteConfigController@UserDestroy']);
            }
        );

        Route::middleware('role:config')->group(function () {
            Route::prefix('platform-config')->group(function () {
                Route::get('/platform-profile', ['as' => 'platform-profile.edit', 'uses' => 'PlatformSiteConfigController@platformProfileEdit']);
                Route::post('/platform-profile/store', ['as' => 'platform-profile.store', 'uses' => 'PlatformSiteConfigController@platformProfileStore']);
            });

            Route::get('valid-url', ['as' => 'platform-config.valid-url', 'uses' => 'PlatformSiteConfigController@validUrlOfficial']);
            Route::get('on-off', ['as' => 'platform-config.on-off', 'uses' => 'PlatformSiteConfigController@onOff']);
        });

        Route::group(
            ['prefix' => 'getnet'],
            function () {

                Route::group(['prefix' => 'plans'], function () {
                    Route::get('', ['as' => 'getnet.plans', 'uses' => 'Getnet\PlanController@index']);
                    Route::get('links', ['as' => 'getnet.plans.links', 'uses' => 'Getnet\PlanController@linksPlans']);
                    Route::get('integrate-all', ['as' => 'getnet.plans.integrate-all', 'uses' => 'PlanController@integrateAll']);
                    Route::get('{plan_id}', ['as' => 'getnet.plans.get', 'uses' => 'Getnet\PlanController@getPlan']);
                    Route::post('{plan_id}', ['as' => 'getnet.plans.update', 'uses' => 'Getnet\PlanController@updatePlan']);
                    Route::post('store', ['as' => 'getnet.plans.store', 'uses' => 'Getnet\PlanController@store']);
                    Route::post('status/{plan_id}/{status}', ['as' => 'getnet.plans.status', 'uses' => 'Getnet\PlanController@updateStatusPlan']);
                });

                Route::group(['prefix' => 'clients'], function () {
                    Route::get('', ['as' => 'getnet.clients', 'uses' => 'Getnet\ClientController@index']);
                    Route::get('integrate-all', ['as' => 'getnet.subscribers.integrate-all', 'uses' => 'SubscriberController@integrateAll']);
                    Route::get('{customer_id}', ['as' => 'getnet.clients.get', 'uses' => 'Getnet\ClientController@getCustomer']);
                    Route::get('store', ['as' => 'getnet.clients.store', 'uses' => 'Getnet\ClientController@store']);
                });

                Route::group(['prefix' => 'subscriptions'], function () {
                    Route::get('', ['as' => 'getnet.subscriptions', 'uses' => 'Getnet\SubscriptionController@index']);
                    Route::get('integrate-all', ['as' => 'getnet.subscriptions.integrate-all', 'uses' => 'Getnet\SubscriptionController@integrateAll']);
                    Route::get('import-charges', ['as' => 'getnet.subscriptions.import-charges', 'uses' => 'Getnet\SubscriptionController@importCharges']);
                    Route::get('{subscription_id}', ['as' => 'getnet.subscriptions.get', 'uses' => 'Getnet\SubscriptionController@getSubscription']);
                    Route::post('cancel', ['as' => 'getnet.subscriptions.cancel', 'uses' => 'Getnet\SubscriptionController@cancelSubscription']);
                });

                Route::group(['prefix' => 'sales'], function () {
                    Route::get('', ['as' => 'getnet.sales.index', 'uses' => 'Getnet\SaleController@index']);
                    Route::get('{payment_id}/cancel', ['as' => 'getnet.sales.cancel-payment', 'uses' => 'Getnet\SaleController@cancelPayment']);
                });

                Route::get('', ['as' => 'getnet.index', 'uses' => 'GetnetController@index']);
            }
        );

        Route::middleware('role:email')->group(function () {

            Route::group(
                ['prefix' => 'emails'],
                function () {
                    Route::get('', ['as' => 'emails.index', 'uses' => 'EmailPlatformController@index']);
                    Route::get('list-email/{email}', ['as' => 'emails.list', 'uses' => 'Subscriber\\SubscriberEmailsController@listEmailsPostmark']);
                    Route::get('create', ['as' => 'emails.create', 'uses' => 'EmailPlatformController@create']);
                    Route::post('store', ['as' => 'emails.store', 'uses' => 'EmailPlatformController@store']);
                    Route::get('edit/{id}', ['as' => 'emails.edit', 'uses' => 'EmailPlatformController@edit']);
                    Route::get('customize/{id}', ['as' => 'emails.customize', 'uses' => 'EmailPlatformController@customize']);
                    Route::post('update/{id}', ['as' => 'emails.update', 'uses' => 'EmailPlatformController@update']);
                    Route::delete('{id}', ['as' => 'emails.destroy', 'uses' => 'EmailPlatformController@destroy']);
                    Route::get('getMessageExample/{id}', ['as' => 'emails.getMessageExample', 'uses' => 'EmailPlatformController@getMessageExample']);
                    Route::get('conf', ['as' => 'emails.conf', 'uses' => 'EmailPlatformController@confEmail']);
                    Route::post('conf/store', ['as' => 'emails.conf.store', 'uses' => 'EmailPlatformController@confEmailStore']);
                    Route::get('test', ['as' => 'emails.test', 'uses' => 'EmailPlatformController@emailTest']);
                    Route::get('valid/{platform_id}/{email}', ['as' => 'emails.valid', 'uses' => 'EmailPlatformController@validEmail']);
                    Route::get('valid-email', ['as' => 'emails.valid-email', 'uses' => 'EmailPlatformController@validEmailChecked']);
                    Route::prefix('ruler')->group(function () {
                        Route::get('', 'RulerController@index')->name('ruler.index');
                        Route::post('save', 'RulerController@save')->name('ruler.save');
                    });

                    Route::group(['prefix' => 'ajax'], function () {
                        Route::get('custom', ['as' => 'emails.ajax.custom', 'uses' => 'EmailPlatformController@ajaxEmailCustom']);
                        Route::get('default', ['as' => 'emails.ajax.default', 'uses' => 'EmailPlatformController@ajaxEmailDefault']);
                    });
                }
            );
        });


        Route::group(
            ['prefix' => 'getnet'],
            function () {
                Route::get('{platform_id}/{plan_id}/c/{course_id?}', ['as' => 'getnet.register', 'uses' => 'GetnetController@register']);

                Route::post('store', ['as' => 'getnet.store', 'uses' => 'GetnetController@subscriberStore']);

                Route::get('thanks/{platform_id}/{plan_id}/{subscriber_id}/c/{course_id?}', ['as' => 'getnet.thanks', 'uses' => 'GetnetController@thanks']);

                Route::get('{platform_id}/{plan_id}/{subscriber_id}/c/{course_id?}', ['as' => 'getnet.checkout', 'uses' => 'GetnetController@cardRegister']);

                Route::post('card-store', ['as' => 'getnet.card.store', 'uses' => 'GetnetController@cardStore']);

                Route::get('subscription-charges', ['as' => 'getnet.subscription-charges', 'uses' => 'GetnetController@charges']);
            }
        );

        Route::resource('quiz', 'QuizController');

        Route::prefix('quiz')->group(function () {
            Route::prefix('{quiz_id}')->group(function () {
                Route::resource('question', 'QuestionController');
            });
        });

        /** Gamification Routes */
        Route::get('gamification-next', 'Api\GamificationController@index')->name('gamification.index');
        Route::prefix('gamification')->group(function () {
            Route::prefix('config')->group(function () {
                Route::get('', 'Api\GamificationController@configurations')->name('gamification.configuration');
                Route::get('/settings', 'Api\GamificationController@getSettings')->name('gamification.get.settings');
                Route::post('/settings', 'Api\GamificationController@saveSettings')->name('gamification.save.settings');

                Route::prefix('actions')->group(function () {
                    Route::get('', 'Api\GamificationController@getActions')->name('gamification.get.actions');
                    Route::post('save', 'Api\GamificationController@saveActions')->name('gamification.save.actions');
                });

                Route::prefix('phases')->group(function () {
                    Route::get('', 'Api\GamificationController@getPhases')->name('gamification.get.phases');
                    Route::post('', 'Api\GamificationController@savePhase')->name('gamification.save.phases');
                    Route::put('{id}', 'Api\GamificationController@updatePhase')->name('gamification.update.phases');
                    Route::delete('{id}', 'Api\GamificationController@deletePhase')->name('gamification.delete.phases');
                });
            });

            Route::get('reports', 'Api\GamificationController@reports')->name('gamification.reports');

            //challenges routes
            Route::prefix('challenges')->group(function () {

                Route::get('', 'Api\GamificationController@challenges')->name('gamification.challenges');
                Route::get('datatable', 'Api\GamificationController@getChallengesDatatable')->name('gamification.get.challenges.datatable');
                Route::post('', 'Api\GamificationController@saveChallenge')->name('gamification.save.challenges');
                Route::put('{id}', 'Api\GamificationController@updateChallenge')->name('gamification.update.challenges');
                Route::delete('{id}', 'Api\GamificationController@deleteChallenge')->name('gamification.delete.challenges');

                Route::prefix('settings')->group(function () {
                    Route::get('', 'Api\GamificationController@getChallengeSettings')->name('gamification.get.challenges.settings');
                    Route::post('', 'Api\GamificationController@saveChallengeSettings')->name('gamification.save.challenges.settings');
                    Route::put('{id}', 'Api\GamificationController@updateChallengeSettings')->name('gamification.update.challenges.settings');
                });
            });

            /** Get dashboard data */
            Route::prefix('dashboard')->group(function () {
                Route::prefix('get')->group(function () {
                    Route::get('status', 'Api\GamificationController@getStatus')->name('gamification.dashboard.get.status');
                    Route::get('outstanding', 'Api\GamificationController@getOutstanding')->name('gamification.dashboard.get.outstanding');
                    Route::get('noengagement', 'Api\GamificationController@getNoEngagement')->name('gamification.dashboard.get.noengagement');
                    Route::get('challenges/{type?}', 'Api\GamificationController@getChallenges')->name('gamification.dashboard.get.challenges');
                });
            });
        });

        /** Content API :: Learning Area Routes */
        Route::prefix('learning-area')->group(function () {
            // Utils Routes
            Route::get('/producer-connect', [LearningAreaController::class, 'getAccess'])->name('learning.area.get.producer.access');
            Route::post('/upload-image', [LearningAreaController::class, 'uploadImage'])->name('learning.area.upload.image');
            Route::post('/get-subscriber', [LearningAreaController::class, 'getSubscriberInfo'])->name('learning.area.get.subscriber.info');

            Route::get('/', [LearningAreaController::class, 'index'])->name('learning.area.index');

            Route::prefix('sections')->group(function () {
                Route::get('/', [LearningAreaController::class, 'index']);
                Route::get('/{id?}/edit', [LearningAreaController::class, 'index']);
            });

            Route::prefix('courses')->group(function () {
                Route::get('/', [LearningAreaController::class, 'index']);
                Route::get('/new', [LearningAreaController::class, 'index']);
                Route::get('/{id?}/edit', [LearningAreaController::class, 'index']);
            });

            Route::prefix('content')->group(function () {
                Route::get('/', [LearningAreaController::class, 'index']);
                Route::get('/new', [LearningAreaController::class, 'index']);
                Route::get('/{id?}/edit', [LearningAreaController::class, 'index']);
                Route::get('/{id?}/new', [LearningAreaController::class, 'index']);
            });

            Route::prefix('authors')->group(function () {
                Route::get('/', [LearningAreaController::class, 'index']);
                Route::get('/new', [LearningAreaController::class, 'index']);
                Route::get('/{id?}/edit', [LearningAreaController::class, 'index']);
            });

            Route::prefix('lives')->group(function () {
                Route::get('/', [LearningAreaController::class, 'index']);
                Route::get('/new', [LearningAreaController::class, 'index']);
                Route::get('/{id?}/edit', [LearningAreaController::class, 'index']);
            });

            Route::prefix('comments')->group(function () {
                Route::get('/', [LearningAreaController::class, 'index']);
                // Route::get('/new', [LearningAreaController::class, 'index']);
                // Route::get('/{id?}/edit', [LearningAreaController::class, 'index']);
                // Route::get('/{id?}/new', [LearningAreaController::class, 'index']);
            });

            Route::prefix('design')->group(function () {
                Route::get('/', [LearningAreaController::class, 'index']);
                Route::get('/onboarding', [LearningAreaController::class, 'index']);
                Route::get('/start-page', [LearningAreaController::class, 'index']);
                Route::get('/config-menu', [LearningAreaController::class, 'index']);
                Route::get('/visual-identity', [LearningAreaController::class, 'index']);
                //                Route::get('/onboard', [LearningAreaController::class, 'onboard'])->name('learning.area.design.onboard.get');
            });
        });

        Route::middleware('role:engagement')->group(function () {
            Route::prefix('audience')->group(function () {
                Route::get('datatables', 'AudienceController@datatables')->name('audience.datatables');
                Route::post('condition/datatables', 'AudienceConditionController@datatables')->name('condition.datatables');

                Route::post('condition/export-csv', 'AudienceConditionController@exportCsv')->name('condition.export-csv');
                Route::post('condition/export-xlsx', 'AudienceConditionController@exportXlsx')->name('condition.export-xlsx');
            });
            Route::resource('audience', 'AudienceController')->except('show');
            Route::prefix('campaign')->group(function () {
                Route::get('get-data', ['as' => 'datatables.campaign', 'uses' => 'CampaignController@contentData']);
                Route::get('get-automatic-ids/{type}', ['as' => 'datatables.get-automatic-ids', 'uses' => 'CampaignController@getAutomaticIds']);
            });
            Route::resource('campaign', 'CampaignController');
        });

        // @todo move this endpoint to "mobile." group
        Route::prefix('push-notification')->group(function () {
            Route::get('/', [ResourcesController::class, 'index'])->name('push-notification.index');
        });

        Route::prefix('mobile')->name('mobile.')->group(function () {
            Route::get('/push-notifications', [
                \App\Http\Controllers\Mobile\MobilePushNotificationController::class, 'index'
            ])->name('notification.index');

            Route::post('/push-notifications', [
                \App\Http\Controllers\Mobile\MobilePushNotificationController::class, 'store'
            ])->name('notification.store');

            Route::patch('/push-notifications/{notification_id}', [
                \App\Http\Controllers\Mobile\MobilePushNotificationController::class, 'update'
            ])->name('notification.update');

            Route::delete('/push-notifications/{notification_id}', [
                \App\Http\Controllers\Mobile\MobilePushNotificationController::class, 'destroy'
            ])->name('notification.destroy');
        });

        Route::prefix('callcenter')->middleware('role:callcenter')->group(function () {
            Route::prefix('attendant')->group(function () {
                Route::get('get-data', ['as' => 'datatables.attendant', 'uses' => 'AttendantController@contentData']);
                Route::get('send-mail-link-callcenter/{id}', ['as' => 'attendant.send-mail-link-callcenter', 'uses' => 'AttendantController@sendMailLinkCallcenter']);
            });
            Route::get('/', ['as' => 'callcenter.dashboard', 'uses' => 'CallCenterReports@dashboard']);

            Route::resource('attendant', 'AttendantController');
            Route::get('attendant/restore/{id}', ['as' => 'attendant.restore', 'uses' => 'AttendantController@restore']);

            Route::prefix('config')->group(function () {
                Route::get('/', ['as' => 'callcenter.config', 'uses' => 'CallCenterConfigController@index']);
                Route::put('update/{id}', ['as' => 'callcenter.config.update', 'uses' => 'CallCenterConfigController@update']);
            });

            Route::prefix('reports')->group(function () {
                Route::get('/', ['as' => 'callcenter.reports', 'uses' => 'CallCenterReports@index']);
                Route::get('attendant/{id}', ['as' => 'callcenter.reports.attendant', 'uses' => 'CallCenterReports@index']);
                Route::get('public', ['as' => 'callcenter.reports.public', 'uses' => 'CallCenterReports@publicReports']);
                Route::get('public/data', ['as' => 'callcenter.reports.public.data', 'uses' => 'CallCenterReports@getPublicReports']);

                Route::prefix('show')->group(function () {
                    Route::get('{id}', ['as' => 'callcenter.reports.show', 'uses' => 'CallCenterReports@show']);
                    Route::get('{id}/{type}', ['as' => 'callcenter.reports.show.request', 'uses' => 'CallCenterReports@show']);
                });

                Route::get('create', function () {
                    return view('callcenter.reports.index');
                })->name('callcenter.reports.create');

                Route::get('get-list', ['as' => 'callcenter.reports.get-list', 'uses' => 'CallCenterReports@getReportsList']);
                Route::get('get-list/{id}', ['as' => 'callcenter.reports.get-list-by-id', 'uses' => 'CallCenterReports@getReportsList']);

                Route::prefix('dashboard')->group(function () {
                    Route::get('get-attendants/{audiences}', ['as' => 'callcenter.reports.dashboard.get-attendants', 'uses' => 'CallCenterReports@getAttendantsList']);
                    Route::get(
                        '{condition}/per-attendant/{period}/{audiences}',
                        ['as' => 'callcenter.reports.dashboard.info.per-attendant', 'uses' => 'CallCenterReports@infoPerAttendant']
                    );
                    Route::get('get-total-leads/{audiences}', ['as' => 'callcenter.reports.dashboard.get-total-leads', 'uses' => 'CallCenterReports@getTotalLeads']);
                    Route::get('get-total-pending/{audiences}', ['as' => 'callcenter.reports.dashboard.get-total-pending', 'uses' => 'CallCenterReports@getTotalPending']);
                });
            });

            Route::post('audience/end-attendances', ['as' => 'callcenter.audience.end-attendances', 'uses' => 'AudienceController@endAttendance']);
            Route::post('audience/start-attendances-again/{id}', ['as' => 'callcenter.audience.start-attendances-again', 'uses' => 'AudienceController@startAttendanceAgain']);
            Route::post('audience/save-actions', ['as' => 'callcenter.audience.save-actions', 'uses' => 'AudienceController@saveActions']);
            Route::get('audience/get-actions/{id}', ['as' => 'callcenter.audience.get-actions', 'uses' => 'AudienceController@getActions']);
        });
        Route::group(
            ['middleware' => ['role:profile']],
            static function () {
                //Profile
                if (env('APP_ENV') === 'local') Route::get('profile/test', ['as' => 'choose.profile.test', 'uses' => 'ProfileController@test']);
                Route::get('profile/read', ['as' => 'choose.profile.read', 'uses' => 'ProfileController@read']);
                Route::put('profile/update', ['as' => 'choose.profile.store', 'uses' => 'ProfileController@store']);
                Route::get('profile/upload/{face}', ['as' => 'choose.profile.upload.get', 'uses' => 'ProfileController@getUpload']);
                Route::post('profile/upload/{face}', ['as' => 'choose.profile.upload.post', 'uses' => 'ProfileController@postUpload']);
                Route::delete('profile/upload/{face}', ['as' => 'choose.profile.upload.delete', 'uses' => 'ProfileController@deleteUpload']);
            }
        );

        // PLATFORM 2.0
        Route::get('platforms', 'PlatformController@index')->name('choose.platform');
        Route::post('platforms/get-all', 'Api\\PlatformController@searchPlatforms')->name('search.all.platforms');
        Route::put('platforms/change-thumb', 'Api\\PlatformController@changePlatformImg')->name('change.platform.thumb');
        Route::post('platforms', ['as' => 'store.choose.platform', 'uses' => 'PlatformController@choose']);
        Route::post('first-access', 'PlatformUserController@firstAccess')->name('first.access');
        Route::get('my-account', ['as' => 'choose.platform.my.account', 'uses' => function () {
            return view('platforms.my-account');
        }]);
        Route::get('new-platform', ['as' => 'new.platform', 'uses' => 'PlatformController@create']);
        Route::post('new-platform', ['as' => 'new.platform.store', 'uses' => 'PlatformController@store']);
        Route::post('accept-terms', ['as' => 'accept.platform.terms', 'uses' => 'PlatformController@acceptTerms']);

        /** COPRODUCER 2.0 */
        Route::get('/coproducer', 'Api\\CoproducerController@index')->name('coproducer');

        /** AFFILIATIONS */
        Route::group(['prefix' => 'affiliations'], function () {
            Route::get('/', [AffiliationsController::class, 'index'])->name('affiliations');
            Route::get('/products', [AffiliationsController::class, 'index'])->name('affiliations.products');
            Route::get('/products/resume', [AffiliationsController::class, 'index'])->name('affiliations.products.resume');
            Route::get('/products/transactions', [AffiliationsController::class, 'index'])->name('affiliations.products.transactions');
            Route::get('/products/withdraws', [AffiliationsController::class, 'index'])->name('affiliations.products.withdraws');
        });

        Route::group(['prefix' => 'developer'], function () {
            Route::get('/', [DeveloperController::class, 'index'])->name('developer');
        });

        Route::group(['prefix' => 'documents'], function () {
            Route::get('/', [DocumentsController::class, 'index'])->name('documents');
        });

        /** PRODUCER AFFILIATES */
        Route::group(['prefix' => 'affiliates'], function () {
            Route::get('/', [AffiliatesController::class, 'index'])->name('affiliates');
            Route::get('/pending', [AffiliatesController::class, 'index'])->name('affiliates.pending');
            Route::get('/different-status', [AffiliatesController::class, 'index'])->name('affiliates.different-status');
            Route::get('/ranking', [AffiliatesController::class, 'index'])->name('affiliates.ranking');
            Route::get('/events', [AffiliatesController::class, 'index'])->name('affiliates.events');
        });
    }
);

Route::group(
    ['middleware' => 'audit.route'],
    function () {
        Route::get('checkout/{platform_id}/{plan_id}/{course_id?}', ['as' => 'mundipagg.checkout', 'uses' => 'Mundipagg\MundipaggCheckoutController@index']);
        Route::post('checkout/{platform_id}/{plan_id}/{course_id?}', ['as' => 'mundipagg.checkout.save', 'uses' => 'Mundipagg\MundipaggCheckoutController@checkout']);
    }
);

Route::group(
    ['prefix' => 'mundipagg', 'middleware' => 'audit.route'],
    function () {
        Route::post('subscriber', ['as' => 'mundipagg.subscriber.create', 'uses' => 'Mundipagg\MundipaggCheckoutController@saveSubscriber']);
        Route::post('cancelcharge/{platform_id}/{payment_id}', ['as' => 'mundipagg.cancelcharge', 'uses' => 'Mundipagg\SubscriberController@cancelCharge']);
    }
);

// @deprecated
Route::prefix('pagarme')->middleware(['audit.route'])->group(function () {
    Route::post('refund-pix/{platform_id}/{payment_id}', 'Pagarme\SubscriberController@refundPix')->name('pagarme.refund-pix');
    Route::post('refund-boleto/{platform_id}/{payment_id}', 'Pagarme\SubscriberController@refundBoleto')->name('pagarme.refund-boleto');
});


Route::prefix('reports')->group(function () {
    Route::middleware('role:sale|producer')->group(function () {
        /** REPORT SALES 2.0 */
        Route::get('subscription', [SalesReportController::class, 'subscriptionsNext'])->name('reports.subscriptions.next');


        Route::get('sales', 'Reports\SalesReportController@index')->name('reports.sales');
        Route::get('transactions/search', 'Reports\SalesReportController@searchTransactionData')
            ->name('reports.sales.search.transaction');
        Route::get('transactions/metrics', 'Reports\SalesReportController@transactionSaleMetrics')
            ->name('reports.sales.metrics.transactions');
        Route::get('transactions/details/{order_number}', 'Reports\SalesReportController@subscriptionTransactions')
            ->name('reports.sales.transactions.subscription');

        // @deprecated Replaced by 'transactions/*' routes
        Route::get('single/search', 'Reports\SalesReportController@searchSingleData')->name('reports.sales.search.single');
        Route::get('single/metrics', 'Reports\SalesReportController@singleSaleMetrics')->name('reports.sales.metrics.single');

        Route::get('subscriptions/search', 'Reports\SalesReportController@searchSubscriptionData')
            ->name('reports.sales.search.subscription');
        Route::get('subscriptions/metrics', 'Reports\SalesReportController@subscriptionMetrics')
            ->name('reports.sales.metrics.subscription');
        Route::get('subscriptions/details/{subscriber_id}/{plan_id}/{order_number}', 'Reports\SalesReportController@subscriptionPayments')
            ->name('reports.sales.payments.subscription');

        Route::get('no-limit/search', 'Reports\SalesReportController@searchNoLimitData')
            ->name('reports.sales.search.no-limit');
        Route::get('no-limit/metrics', 'Reports\SalesReportController@noLimitMetrics')
            ->name('reports.sales.metrics.no-limit');
        Route::get('payment-commisions/{paymentId}', 'Reports\SalesReportController@getCommisions')
            ->name('reports.payment.commisions');
        Route::get('no-limit/details/{subscriber_id}/{plan_id}/{order_number}', 'Reports\SalesReportController@noLimitPayments')
            ->name('reports.sales.payments.no-limit');

        Route::post('sale-export', ['as' => 'reports.sale-export', 'uses' => 'Reports\SalesReportController@export']);

        /* REPORT FINANCIAL (API) 2.0 */
        Route::get('sales', [SalesReportController::class, 'index'])->name('reports.sales');
        Route::get('/get-transactions', [FinancialController::class, 'getTransactions'])->name('reports.financial.transactions');
        Route::get('/get-transactions/{paymentId}', [FinancialController::class, 'getTransactionsDetails'])->name('reports.financial.transactions.details');
        Route::get('/get-no-limit-transactions', [FinancialController::class, 'getNoLimitTransactions'])->name('reports.financial.nolimit.transactions');
        Route::get('/get-subscriptions', [FinancialController::class, 'getSubscriptions'])->name('reports.financial.subscriptions');
        Route::post('/retry-payment/{paymentId}', [FinancialController::class, 'retryPaymentTransaction'])->name('reports.financial.retry.payment');

        Route::get('/get-no-limit-transactions/{subscriberId}/{planId}/{paymentOrderNumber}', 'Api\FinancialController@getNoLimitTransactionsDetails')->name('reports.financial.nolimit.transactions.details');
        Route::get('/get-subscriptions/{subscriberId}/{planId}/{paymentOrderNumber}', 'Api\FinancialController@getSubscriptionsDetails')->name('reports.financial.subscriptions.details');
    });
});

Route::prefix('reports')->group(function () {
    Route::middleware('role:subscription|producer')->group(function () {
        Route::get('subscription', ['as' => 'reports.subscription', 'uses' => 'Reports\SalesReportController@subscription']);
    });
});

Route::prefix('reports')->middleware('role:content-report')->group(function () {
    Route::get('content', ['as' => 'reports.content', 'uses' => 'Reports\ContentReportController@index']);
});

Route::prefix('reports')->middleware('role:search-report')->group(function () {
    Route::get('content-search', 'Reports\ResearchReportController@index')->name('reports.research');
    Route::get('get-research', 'Reports\ResearchReportController@getResearchAPI')->name('reports.get.research.api');
    // Route::get('content-search', ['as' => 'reports.content-search', 'uses' => 'ReportController@contentSearch']);
    // Route::get('content-search-data', ['as' => 'reports.datatable.contents-search', 'uses' => 'ReportController@contentSearchData']);
});

Route::prefix('reports')->middleware('role:course-report')->group(function () {
    Route::get('course-search', ['as' => 'reports.course-search', 'uses' => 'Reports\CourseReportController@index']);
    Route::get('course-search-data', ['as' => 'reports.datatable.course-search', 'uses' => 'ReportController@courseSearchData']);

    /** Progress Report */
    Route::get('progress', 'Reports\ProgressReportController@index')->name('reports.progress');
    Route::get('get-progress', 'Reports\ProgressReportController@getProgressAPI')->name('reports.get.progress.api');
    Route::get('get-progress-courses', 'Reports\ProgressReportController@getCoursesByPlatform')->name('reports.get.progress.courses.api');
    Route::get('get-subscription-progress', 'Reports\ProgressReportController@getSubscribers')->name('reports.get.progress.subscribers.api');
    Route::get('simplified-progress', 'Reports\ProgressReportController@simplifiedProgress')->name('reports.simplified.progress');
    Route::get('get-subscriber-simplified-progress', 'Reports\ProgressReportController@getSubscriberSimplifiedProgress')->name('reports.get.subscriber.simplified.progress');
});


Route::prefix('reports')->middleware('role:report')->group(function () {

    Route::get('top-users-platform', ['as' => 'reports.top-users-platform', 'uses' => 'ReportController@topUsersPlatform']);
    Route::get('top-users-site', ['as' => 'reports.top-users-site', 'uses' => 'ReportController@topUsersSite']);
    Route::get('users-without-access-site', ['as' => 'reports.users-without-access-site', 'uses' => 'ReportController@usersWithoutAccessSite']);
    Route::get('accessed-sections', ['as' => 'reports.accessed-sections', 'uses' => 'ReportController@accessedSections']);

    Route::get('subscriber', ['as' => 'reports.subscribers.index', 'uses' => 'Reports\SubscriberReportController@index']);

    /* New reports by API */
    Route::get('access', ['as' => 'reports.access', 'uses' => 'Reports\AccessReportController@index']);
    Route::get('client/balance/{recipient_id?}', ['as' => 'client.balance', 'uses' => 'Mundipagg\RecipientController@getClientBalance']);
});

Route::prefix('reports')->middleware('role:financial')->group(function () {
    Route::get('financial', ['as' => 'reports.financial', 'uses' => 'Reports\FinancialReportController@index']);
});

Route::prefix('reports')->middleware('role:lists')->group(function () {
    /* Downloads */
    Route::prefix('downloads')->group(function () {
        Route::get('', ['as' => 'reports.downloads', 'uses' => 'DownloadController@index']);
        Route::post('subscriber-user', ['as' => 'report.download.subscribers.user', 'uses' => 'SubscriberController@exportSubscriber']);
        Route::post('lead', ['as' => 'report.download.lead', 'uses' => 'LeadController@exportLeads']);
    });
});

Route::group(['prefix' => 'api', 'middleware' => ['verify.user.logged.web.route']], function () {

    Route::get('time-fees', 'Api\TimeAndFeesController@information')->name('documents.api.timeFees');
    Route::post('validate-documents', 'Api\ValidateDocumentsController@validateDocuments');

    Route::group(['prefix' => 'mobile-notifications'], function () {
        Route::get('notifications', 'MobileNotification\MobileNotificationController@index');
        Route::get('notifications/{id}', 'MobileNotification\MobileNotificationController@show');
        Route::put('update-notification/{id}', 'MobileNotification\MobileNotificationController@update');
    });

    // Relatórios
    Route::group(['prefix' => 'reports'], function () {
        /* ACCESS REPORT */
        Route::get('hits-per-hour', 'Reports\AccessReportController@hitsHourDay');
        Route::get('hits-per-day', 'Reports\AccessReportController@hitsPerDay');
        Route::get('hits-per-day-week', 'Reports\AccessReportController@hitsDayWeek');
        Route::get('age-gender', 'Reports\AccessReportController@ageGender');
        Route::get('gender', 'Reports\AccessReportController@gender');
        Route::get('hits-by-location', 'Reports\AccessReportController@hitsByLocation');
        Route::get('avg-time-access', 'Reports\AccessReportController@avgAccessTime');
        /* CONTENT REPORT */
        Route::get('most-accessed-section', 'Reports\ContentReportController@mostAccessedSection');
        Route::get('most-accessed-content', 'Reports\ContentReportController@mostAccessedContent');
        Route::get('most-liked-content', 'Reports\ContentReportController@mostLikedContent');
        Route::get('count-commented-content', 'Reports\ContentReportController@countCommentedContent');
        Route::get('most-accessed-content-by-author', 'Reports\ContentReportController@contentMostAccessedByAuthor');
        Route::get('total-viewed-content-by-author', 'Reports\ContentReportController@contentViewsByAuthor');
        /* COURSE REPORT */
        Route::get('get-most-viewed-courses', 'Reports\CourseReportController@getMostViewedCourses');
        Route::get('get-most-viewed-courses-by-course', 'Reports\CourseReportController@getMostViewedCourseByDayWeek');
        Route::get('subscriber-course', 'Reports\CourseReportController@getSubscriberCourses');
        Route::get('subscriber-with-course', 'Reports\CourseReportController@getSubscriberWithCourses');
        Route::get('subscriber-without-course', 'Reports\CourseReportController@getSubscriberWithoutCourses');
        Route::get('subscriber-by-course', 'Reports\CourseReportController@getSubscriberByCourse');
        /* FINANCIAL REPORT */
        Route::prefix('financial')->group(function () {
            Route::get('total-transactions', 'Reports\FinancialReportController@getTotalTransactions');
            Route::get('total-antecipation-fees', 'Reports\FinancialReportController@getTotalAntecipationFees');
            Route::get('average-ticket-transactions', 'Reports\FinancialReportController@getAverageTicketPrice');
            Route::get('sum-transactions', 'Reports\FinancialReportController@getSumTransactions');
            Route::get('percent-type-payment-transactions', 'Reports\FinancialReportController@getPercentTypePayment');
            Route::get('status-transactions', 'Reports\FinancialReportController@getTransactionByStatus');
            Route::get('card-multiples', 'Reports\FinancialReportController@getTotalCardMultiples');
            Route::get('generated-paid-transactions', 'Reports\FinancialReportController@getGeneratedVsPaid');
            Route::get('total-to-receive', 'Reports\FinancialReportController@getToReceive');
            Route::get('total-billing', 'Reports\FinancialReportController@getTotalBilling');
            Route::get('sales-forecast', 'Reports\FinancialReportController@getSalesForecast');
            Route::get('card-brands', 'Reports\FinancialReportController@getCreditCardBrands');
            Route::get('status-graph-transactions', 'Reports\FinancialReportController@graphTransactionByStatus');
            Route::get('status-graph-credit-card-transactions', 'Reports\FinancialReportController@graphCreditCardStatusTransactions');
            Route::get('installments-transactions', 'Reports\FinancialReportController@graphTransactionsByInstallments');
            Route::get('period-transactions', 'Reports\FinancialReportController@graphTransactionsByPeriod');
            //Route::get('withdrawal-data', 'Mundipagg\RecipientController@listWithdrawalsDatatablesData')->name('recipient.withdrawal.list.datatables');
            Route::get('withdrawal-data', 'Mundipagg\RecipientController@listWithdrawalsClient')->name('recipient.withdrawal.list.datatables');
        });
        /* SUBSCRIBERS REPORT */
        Route::prefix('subscribers')->group(function () {
            Route::get('get-all', ['as' => 'reports.subscribers.getall', 'uses' => 'Reports\SubscriberReportController@getSubscribers']);
        });

        /* RANKING REPORT */
        Route::prefix('affiliates-reports')->group(function () {
            Route::get('affiliate-ranking', 'Affiliations\AffiliatesApiController@affiliateRanking')->name('affiliate.ranking');
        });

        /* TOKEN FOR LA */
        Route::get('la/token', ['as' => 'api.la.token', 'uses' => 'LearningAreaController@generateTokenLA']);

        /* SALES */
        Route::post('export-report', ['as' => 'report.sales.export.data', 'uses' => 'Reports\SalesReportController@exportReports']);
        Route::get('financial-export-report', [SalesReportController::class, 'financialExportReports'])->name('financial.report.sales.export.data');

        /**
         * TODO : Route 'chargeback' removed to avoid conflicting chargeback calls
         */
        // Route::get('chargeback', ['as' => 'report.sales.search.chargeback', 'uses' => 'Pagarme\ChargebackController@chargebackData']);
    });

    /* SUBSCRIBERS */
    Route::get('list-subscribers', 'SubscriberController@subscriberData');

    /* CODE ACTION */
    Route::post('action-send-code', 'CodeActionController@sendCode');
    Route::post('verify-pin-code', 'CodeActionController@verifyPinCode');

    /* COURSE REPORT */
    Route::get('subscribers-status', 'DashboardController@subscribersStatus');
    Route::get('subscribers-status-by-period', 'DashboardController@getSubscribersStatusByPeriod');
    Route::get('subscribers-last-access', 'DashboardController@lastAccess');
    Route::get('subscribers-last-created', 'DashboardController@getNewSubscribers');
    Route::get('subscribers-last-created-by-period', 'DashboardController@getNewSubscribersByPeriod');
    Route::get('subscribers-bar-summary', 'DashboardController@getSubscribersBarSummary');
    Route::get('get-top-10-content', 'DashboardController@getTop10Contents');
    Route::get('get-plans-sales', 'DashboardController@getPlanSales');
    Route::get('get-courses-sales', 'DashboardController@getCoursesSales');
    Route::get('get-online-users', 'DashboardController@getOnlineUsers');
    Route::get('get-all-products', 'DashboardController@getProductsByPlatform')->name('get.all.products');
    Route::get('get-product-sale-by-period', 'DashboardController@getProductSaleByPeriod')->name('get.product.sale.by.period');

    /* COMMENTS API */
    Route::get('get-all-comments', 'CommentsController@getAllComments');
    Route::get('get-comment', 'CommentsController@getComment');
    Route::get('get-comment', 'CommentsController@getComment');
    Route::post('reply-comment', 'CommentsController@sendReplyComment');
    Route::get('replies-by-comment', 'CommentsController@getRepliesByCommentId');
    Route::get('hidden-comment', 'CommentsController@hiddenComment');
    Route::get('delete-comment', 'CommentsController@destroy');

    /* FORUM */
    Route::get('forum/get-all-posts', 'ForumPostController@getAllPosts');
    Route::post('forum/approve-or-deny-post', 'ForumPostController@postModeration');
    Route::post('forum/delete-post', 'ForumPostController@postDelete');
    Route::get('forum/get-replies', 'ForumPostController@getRepliesByPostID');
    Route::post('forum/send-reply-post', 'ForumPostController@sendReplyPost');
    Route::post('forum/delete-reply', 'ForumPostController@deleteReplyPost');
    Route::post('forum/change-status-reply', 'ForumPostController@changeStatusReplyPost');

    Route::group(['prefix' => 'payments'], function () {
        Route::get('/{payment}/send-refund', [PaymentController::class, 'sendRefund'])->name('api.send.refund');
        Route::get('/{payment}', [PaymentController::class, 'getData'])->name('api.get.refund.proof');
        Route::get('/{payment}/send-purchase-proof', [PaymentController::class, 'sendPurchaseProof'])->name('api.send.buyed.proof');
        Route::get('/{payment}/send-bank-slip', [PaymentController::class, 'sendBankSlip'])->name('api.resend.boleto');
        Route::post('/{payment}/refunds/credit-cards/{single?}', 'PaymentController@refundCreditCard');
    });

    /* PLANS */
    Route::post('plans/get-plans-by-product', 'PlanController@getPlansByProducts');
    Route::get('plans/get-all', 'PlanController@getPlans')->name('plans.getAllPlans');
    Route::post('subscriptions/add', 'SubscriptionController@subscriberManualAdd')->name('subscription.manual.add');
    Route::post('subscriptions/change-product', 'SubscriptionController@changeSubscriptionStatus')->name('subscription.change.product');

    /* DOWNLOADS */
    Route::get('downloads/get-all', ['as' => 'api.downloads.getall', 'uses' => 'DownloadController@getAllDownloads']);
    //    Route::get('downloads/pdf', ['as' => 'api.downloads.pdf', 'uses' => 'Reports\SalesReportController@testepdf']);

    /* CHECKOUT 2.0 */
    Route::group(['prefix' => 'checkout'], function () {
        Route::get('/transfers', 'Api\CheckoutController@listTransfers')->name('api.checkout.list');
        Route::post('/refund', [CheckoutController::class, 'refund'])->name('api.checkout.refund');
    });

    /* AUTHORS 2.0 */
    Route::middleware('role:author')->group(
        function () {
            Route::prefix('authors')->group(function () {
                Route::get('list', [AuthorController::class, 'list'])->name('api.authors.list');
                Route::get('to-list', [AuthorController::class, 'getAuthorToList'])->name('api.authors.tolist');
                Route::post('delete-photo', [AuthorController::class, 'deletePhoto'])->name('api.authors.delete_photo');
                Route::put('{id}/status', [AuthorController::class, 'status'])->name('api.authors.update_status');
            });
        }
    );
    Route::prefix('transfer-content')->middleware('role:transfer-content')->group(function () {
        Route::post('', [AuthorController::class, 'transferContent'])->name('api.authors.content');
        Route::get('', [AuthorController::class, 'show'])->name('api.transfer.content.index');
    });

    /* PRODUCERS 2.0 */
    Route::group(['prefix' => 'producers'], function () {
        Route::get('/{productId}', 'Api\ProducerController@getProducersByProductId')->name('api.producers.get.all');
        Route::post('/{productId}', 'Api\ProducerController@store')->name('api.producers.send.invite');
        Route::put('/{productId}', 'Api\ProducerController@update')->name('api.producers.update');
        Route::post('/{productId}/cancel', 'Api\ProducerController@cancelContract')->name('api.producers.cancel.contract');
    });

    /* CO-PRODUCERS 2.0 */
    Route::group(['prefix' => 'coproducers', 'namespace' => 'CoProducer'], function () {
        Route::get('/active-platforms-coproducer', 'CoProducerApiController@getPlatformsCoProducersAffiliationsActive')->name('active.platforms.coproducer');
        Route::get('/pending-platforms-coproducer', 'CoProducerApiController@getPlatformsCoProducersAffiliationsPending')->name('pending.platforms.coproducer');
        Route::post('/update-status-producer-products/{id}/{producerId}', 'CoProducerApiController@updateStatusProducerProducts')->name('update.status.producer.products.coproducer');
        Route::put('/accept-co-production-request/{producer_products_id}/{producerId}', 'CoProducerApiController@acceptCoProductionRequest')->name('accept.co.production.request');

        Route::group(['prefix' => '/{platformId}', 'middleware' => [\App\Http\Middleware\CheckProducers::class]], function ($platformId) {
            Route::get('/get-bank-information-coproducer', 'CoProducerApiController@getRegisteredBankInformationCoProducerAffiliations')->name('get.bank.information.coproducer');
            Route::post('/update-bank-information-coproducer', 'CoProducerApiController@updateBankData')->name('update.bank.information.coproducer');
            Route::post('/validade-document-coproducer', 'CoProducerApiController@validateDocuments')->name('validade.document.coproducer');
            Route::get('/balance', 'CoProducerApiController@balance')->name('coproducer.balance');
            Route::get('/list-withdrawals', 'CoProducerApiController@listWithdrawals')->name('list.withdrawals.coproducer');
            Route::post('/withdraw-value', 'CoProducerApiController@withdrawValue')->name('coproducer.withdraw.value');
            Route::get('/financial-report-sales', 'CoProducerApiController@financialReportSales')->name('financial.report.sales.coproducer');
            Route::post('/sale-details', 'CoProducerApiController@saleDetails')->name('sale.details.coproducer');
        });
    });

    /* AFFILIATIONS */
    Route::group(['prefix' => 'affiliations', 'namespace' => 'Affiliations'], function () {
        Route::get('/active-platforms-affiliate', 'AffiliationsApiController@getPlatformsAffiliations')->name('active.platforms.affiliate');
        Route::get('/pending-platforms-affiliate', 'AffiliationsApiController@getPlatformsCoProducersAffiliationsPending')->name('pending.platforms.affiliate');
        Route::post('/update-status-producer-products/{id}/{producerId}', 'AffiliationsApiController@updateStatusProducerProducts')->name('update.status.producer.products');
        Route::post('/update-commission-producer-products/{producerProductId}', 'AffiliationsApiController@updateCommissionProducerProducts')->name('update.commission.producer.products');
        Route::get('/affiliations-list-links/{product_id}', 'AffiliationsApiController@listLinksOfAffiliate')->name('affiliations.list.links');
        Route::get('/affiliations-get-filters/{platformId}', 'AffiliationsApiController@affiliateFilters')->name('affiliations.get.filters');
        Route::get('/affiliations-by-status', 'AffiliationsApiController@listAllAffiliatesByStatus')->name('affiliations.by.status');
        Route::get('/buyer', [\App\Http\Controllers\Affiliations\AffiliatesEventsApiController::class, 'getAffiliatesBuyerInformation'])->name('affiliations.buyer');

        Route::group(['prefix' => '/{platformId}', 'middleware' => [\App\Http\Middleware\CheckProducers::class]], function ($platformId) {
            Route::get('/get-bank-information-affiliate', 'AffiliationsApiController@getRegisteredBankInformationCoProducerAffiliations')->name('get.bank.information.affiliate');
            Route::post('/update-bank-information-affiliate', 'AffiliationsApiController@updateBankData')->name('update.bank.information.affiliate');
            Route::post('/validade-document-affiliate', 'AffiliationsApiController@validateDocuments')->name('validade.document.affiliate');
            Route::get('/balance', 'AffiliationsApiController@balance')->name('affiliate.balance');
            Route::get('/list-withdrawals', 'AffiliationsApiController@listWithdrawals')->name('list.withdrawals');
            Route::post('/withdraw-create', 'AffiliationsApiController@withdrawCreate')->name('affiliate.withdraw.create');
            Route::get('/financial-report-sales', 'AffiliationsApiController@financialReportSales')->name('financial.report.sales.affiliate');
            Route::post('/sale-details', 'AffiliationsApiController@saleDetails')->name('sale.details.affiliate');
            Route::get('/affiliations-products-list', 'AffiliationsApiController@listProductsAffiliates')->name('affiliations.products.list');
            Route::get('/affiliate-detail/{producerProductId}', 'AffiliationsApiController@getDetailOfAffiliate')->name('affiliate.detail');
            Route::post('/affiliate-change-status/{producerProductId}', 'AffiliationsApiController@changeAffiliateStatusById')->name('affiliate.change.status');
            Route::get('/affiliate-user-data/{producerId}', 'AffiliationsApiController@getUserAffiliateData')->name('affiliate.user.data');

            Route::get('/affiliates/active', [
                \App\Http\Controllers\Affiliations\AffiliatesApiController::class, 'listActiveAffiliates'
            ])->name('affiliations.list.active');

            Route::get('/affiliates/all', [
                \App\Http\Controllers\Affiliations\AffiliatesApiController::class, 'listAllAffiliates'
            ])->name('affiliations.list.all');

            Route::get('/affiliates/events', [
                \App\Http\Controllers\Affiliations\AffiliatesEventsApiController::class, 'getAffiliatesEventsInformation'
            ])->name('affiliations.list.events');

            Route::get('/affiliates/events/filters', [
                \App\Http\Controllers\Affiliations\AffiliatesEventsApiController::class, 'getAffiliateEventsFilters'
            ])->name('affiliations.events.filters');

            Route::delete('/contracts/{producer_product_id}', [
                \App\Http\Controllers\Affiliations\AffiliationContractController::class, 'cancelAffiliationContract'
            ])->name('affiliations.contracts.cancel');

            Route::delete('/contracts/{producer_product_id}/block', [
                \App\Http\Controllers\Affiliations\AffiliationContractController::class, 'blockAffiliationContract'
            ])->name('affiliations.contracts.block');

            Route::post('/contracts/{producer_product_id}/unblock', [
                \App\Http\Controllers\Affiliations\AffiliationContractController::class, 'unblockAffiliateByContract'
            ])->name('affiliations.contracts.unblock');
        });
    });

    /* FIRST ACCESS */
    Route::group(['prefix' => 'first-access'], function () {
        Route::post('update-bank-data', 'BankDataController@updateBankInformationFirstAccess')->name('first.access.update.bank');
        Route::post('update-address', 'PlatformUserController@updateAddress')->name('first.access.update.address');
        Route::post('get-client-data', 'PlatformUserController@getClientData')->name('first.access.get.client.data');
        //Route::post('validate-documents/{face}', 'PlatformUserController@validateDocuments')->name('first.access.validate.documents');
    });

    /* MY DATA */
    Route::group(['prefix' => 'my-data'], function () {
        Route::get('get-address', 'MyDataController@getAddress')->name('my.data.get.address');
        Route::put('update-address', 'MyDataController@updateAddress')->name('my.data.update.address');
        Route::get('send-authorization-token', 'MyDataController@sendAuthorizationToken')->name('my.data.send-authorization-token');
        Route::get('verify-authorization-token', 'MyDataController@verifyAuthorizationToken')->name('my.data.verify-authorization-token');
        Route::group(['prefix' => 'bank-details'], function () {
            Route::get('', 'MyDataController@getBankDetails')->name('my.data.get.bank.details');
            Route::put('', 'MyDataController@updateBankDetails')->name('my.data.update.bank.details');
        });
        //Route::get('get-identity', 'MyDataController@getIdentity')->name('my.data.get.identity');
        Route::post('store-identity', 'MyDataController@storeIdentity')->name('my.data.store.identity');
        Route::get('get-identity', 'MyDataController@getIdentity')->name('my.data.get.identity');
        //Route::put('update-identity', 'MyDataController@validateDocuments')->name('my.data.update.identity');
    });

    /* DEFAULT */
    Route::get('banks', [BankDataController::class, 'getBankList'])->name('default.get.banks');

    /* AFFILIATIONS */
    Route::group(['prefix' => 'affiliations', 'namespace' => 'AffiliateSettings'], function () {
        Route::post('create-or-update', 'AffiliateSettingsController@createOrUpdate')->name('affiliate.create-or-update');
    });
});

Route::get('send-campaign-pending', 'CampaignController@sendCampaignPending');
Route::get('send-notification-pending', 'CampaignController@sendNotificationPending');

Route::get('/mailable/{subscriber_id}', function ($subscriber_id) {
    $emailData = ['subscriber_id' => $subscriber_id];
    return new App\Mail\SendMailAuto($emailData);
});

Route::get('/mail-test/{subscriber_id}', 'SubscriberController@mailTest');

//0d36c33c-8500-4d09-867b-1455b1ab6d2a

Route::get('muda-senha', function () {

    $platform_user = new \App\PlatformUser();

    foreach ($platform_user->where('platform_id', '6971461e-f125-4707-910f-32c60317d3e5')->get() as $user) {
        $user->update(['password' => Hash::make('123456')]);
    }

    echo 'senha alterada';
});

Route::get('resend-data-to-non-logged', ['as' => 'subscribers.resend-data-to-non-logged', 'uses' => 'SubscriberController@resendDataToNonLogged']);

//Recipient routes
Route::group(['middleware' => ['auth2f', 'audit.route', 'verify.ip'], 'prefix' => 'recipient'], function () {
    Route::get('info', [\App\Http\Controllers\Financial\BankingController::class, 'getBankAccountData'])->name('recipient.info');
    Route::get('balance', [\App\Http\Controllers\Financial\BankingController::class, 'getClientBalance'])->name('recipient.balance');
    Route::get('withdrawal', ['as' => 'recipient.withdrawal.list', 'uses' => 'Mundipagg\RecipientController@listWithdrawals']);
    Route::get('withdrawal/{withdrawal_id}', ['as' => 'recipient.withdrawal.get', 'uses' => 'Mundipagg\RecipientController@getWithdrawal']);
    Route::post('withdrawal', ['as' => 'recipient.withdrawal.send', 'uses' => 'Mundipagg\RecipientController@sendWithdrawal']);
    Route::post('transfer-settings', ['as' => 'recipient.withdrawal.send', 'uses' => 'Mundipagg\RecipientController@setTransferSettings']);
    Route::post('automatic-anticipation-settings', ['as' => 'recipient.withdrawal.send', 'uses' => 'Mundipagg\RecipientController@setAutomaticAnticipationSettings']);
    Route::get('antecipation-limits', ['as' => 'recipient.antecipation.limits', 'uses' => 'Mundipagg\RecipientController@getAntecipationLimits']);
    Route::get('antecipation', ['as' => 'recipient.antecipation.list', 'uses' => 'Mundipagg\RecipientController@listAntecipations']);
    Route::post('antecipation', ['as' => 'recipient.antecipation.send', 'uses' => 'Mundipagg\RecipientController@sendAntecipation']);
    Route::post('antecipation-confirm/{antecipation_id}', ['as' => 'recipient.antecipation.confirm', 'uses' => 'Mundipagg\RecipientController@confirmAntecipation']);
});

Route::get('creditcard', ['as' => 'creditcard.list', 'uses' => 'Mundipagg\CreditCardController@listCreditCards']);

Route::group(['prefix' => 'test'], function () {

    Route::get('/log/', [\App\Http\Controllers\Test\LogTestController::class, '__invoke']);

    Route::get('/email/{email}', function ($destination) {
        $mail = new App\Mail\BaseTemplate();
        Mail::to($destination)->send($mail);
        return $mail;
    });
});
