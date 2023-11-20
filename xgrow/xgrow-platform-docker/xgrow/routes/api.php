<?php

use App\Http\Controllers\Api\BackofficeController;
use Illuminate\Http\Request;
use App\Content;
use App\Section;

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

Route::group(['prefix' => 'contents'], function () {
    Route::post('comment', 'CommentsController@store');
});

Route::get('teste', function () {
    return "oi";
});

Route::post('login', 'AuthController@login');

Route::group(['middleware' => ['api']], function () {

    //    Route::post('login', 'AuthController@login');
    Route::get('seed-config', 'SeedTemplateController@seedConfig');
    Route::post('checks-if-exists', 'AuthController@checksIfExists');
    Route::post('checks-if-email-exists', 'AuthController@checksIfEmailExists');

    /* Nova recuperação de senha para comunidade */
    //    Route::post('recovery-password', 'AuthController@resetPassword'); // Não usar mais
    //    Route::post('subscriber-password', 'AuthController@newSubscriberPassword'); // Não usar mais
    Route::post('recovery-password', 'AuthController@sendResetLinkEmail');
    Route::post('subscriber-password', 'AuthController@resetLAPassword');
    /* Fim da Nova recuperação de senha para comunidade */

    Route::get('seed-footer', 'SeedTemplateController@seedFooter');

    Route::group(['middleware' => 'jwt.verify'], function () {

        Route::group(['prefix' => 'v2'], function () {
            Route::get('seed-courses', "SeedCourseController@seedCourses");
        });

        Route::post('logout', ['as' => 'api.logout', 'uses' => 'AuthController@logout']);
        Route::post('refresh', 'AuthController@refresh');
        Route::post('check-token', 'AuthController@checkToken');
        Route::post('me', 'AuthController@me');

        /*seeds*/
        Route::get('seed-menu', 'SeedTemplateController@seedMenu');
        Route::get('seed-accept-terms', 'SubscriberController@seedAcceptTerms');
        Route::post('seed-user-info', 'SubscriberController@seedUserInfo');
        Route::get('get-user-info', 'SubscriberController@getUserInfo');
        Route::get('seed-welcome', 'SeedTemplateController@seedWelcome');
        Route::get('seed-section', 'SeedTemplateController@seedSection');
        Route::get('seed-platform-template', "SeedTemplateController@seedPlatformTemplate");
        Route::get('seed-get-content-by-category', "SeedTemplateController@getContentByCategory");

        Route::get('seed-all-sections', 'SeedSectionController@seedAllSections');
        Route::get('seed-all-contents', 'SeedSectionController@seedAllContents');
        Route::match(['get', 'post'], 'seed-feature-by-order', 'SeedSectionController@seedFeatureByOrder');
        Route::get('seed-contents-from-section', 'SeedSectionController@seedContentsFromSection');
        Route::get('seed-get-contents-from-the-feature-order', 'SeedSectionController@seedContentsFromTheFeatureOrder');
        Route::get('seed-get-latest-section-content', 'SeedSectionController@seedLatestSectionContent');

        Route::get('seed-get-section-config', 'SeedSectionController@seedSectionConfig');

        Route::get('seed-content-by-id', 'SeedContentController@seedContentById');
        Route::post('seed-content-likes', 'SeedContentController@seedContentLikes');
        Route::post('seed-content-views', 'SeedContentController@seedContentViews');

        Route::post('seed-contents-comments', 'SeedContentController@seedContentsComments');
        Route::get('seed-all-comments', "SeedContentController@seedAllComments");
        Route::get('seed-contents-comments-replies', "SeedContentController@seedAllCommentsReplies");
        Route::post('seed-delete-replies-comments', "SeedContentController@deleteReplyComment");

        Route::get('check-like-content', 'SeedContentController@getContentLike');
        Route::get('seed-courses', "SeedCourseController@seedCourses");
        Route::get('seed-courses-2', "SeedCourseController@seedCourses2");
        Route::get('seed-course-config', "SeedCourseController@getCourseConfig");
        Route::get('seed-class-by-id', "SeedCourseController@seedClassById");
        Route::get('seed-modules-and-classes', "SeedCourseController@seedModulesAndClasses");
        Route::get('seed-set-class-watched', "SeedCourseController@seedSetClassWatched");
        Route::get('seed-teste', "SeedCourseController@seedTeste");
        Route::post('seed-save-note', "SeedCourseController@saveNote");
        Route::get('seed-get-start-content', "SeedCourseController@seedGetStartContent");

        Route::get('get-verify-url', "SeedCourseController@getVerifyUrl");

        Route::get('research', "SeedContentController@getResearchContent");

        Route::post('test', ['as' => 'emails.test', 'uses' => 'EmailPlatformController@emailTest']);

        Route::get('/mailable/{subscriber_id}', function ($subscriber_id) {
            $emailData = ['subscriber_id' => $subscriber_id];
            return new App\Mail\SendMailAuto($emailData);
        });

        /* Rotas do fórum */
        Route::get('forum', "SeedForumController@getForum");
        Route::get('forum/topics/get-all', "SeedForumController@getAllTopics");
        Route::post('forum/topics/get', "SeedForumController@getTopics");
        Route::get('forum/post', "SeedForumController@getPostById");
        Route::get('forum/posts/get-by-topic/{id}', "SeedForumController@getPostsByTopicId");
        Route::post('forum/post', "SeedForumController@sendPost");
        Route::post('forum/post/reply', "SeedForumController@postReply");
        Route::post('forum/post/like', "SeedForumController@likePostAndReply");
        Route::post('forum/post/delete', "SeedForumController@deletePost");
        Route::post('forum/post/update', "SeedForumController@updatePost");

        /* Rotas reservadas para LOGS */
        Route::post('save-access-course-log', 'SeedCourseController@saveAccessCourseLog');
    });

    Route::get('password-fogot', ['as' => 'password-forgot', 'uses' => 'SubscriberController@passwordForgot']);


    Route::group(['prefix' => 'password'], function () {
        Route::get('resetemail/{user_type}', ['as' => 'subscribers.password.reset', 'uses' => 'Auth\ForgotPasswordController@showLinkRequestForm']);
        /**/
        Route::post('email', ['as' => 'subscribers.password.email', 'uses' => 'Auth\ForgotPasswordController@sendResetLinkEmail']);
        Route::get('reset/{token}', ['as' => 'subscribers.password.reset.token', 'uses' => 'Auth\ResetPasswordController@showResetForm']);
        Route::post('repasswd', ['as' => 'subscribers.repasswd.request', 'uses' => 'Auth\ResetPasswordController@repasswd']);
        Route::post('request', ['as' => 'subscribers.password.request', 'uses' => 'Auth\ResetPasswordController@resetForApi']);
    });

    // Getnet site cliente
    Route::group(['prefix' => 'getnet'], function () {
        Route::post('payment-date', 'Getnet\SubscriptionController@paymentDateSubscription');
        Route::post('payment-type/credit/card', 'Getnet\SubscriptionController@paymentTypeCreditCardSubscription');
    });
});

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

        Route::get('payments', 'Api\PaymentController@index')->name('payments');
        Route::post('payments/recurrence', 'Api\PaymentController@recurrenceOrder')->name('payments.recurrence');
        Route::post('payments/unlimited', 'Api\PaymentController@unlimitedOrder')->name('payments.unlimited');

        // Atualizar dados do cartão de crédito
        Route::get('creditcard', ['as' => 'creditcard.list', 'uses' => 'Mundipagg\CreditCardController@listCreditCards']);
        Route::get('creditcard/{id}', ['as' => 'creditcard.get', 'uses' => 'Mundipagg\CreditCardController@getCreditCard']);

        // Store new card
        Route::post('creditcard', ['as' => 'creditcard.store', 'uses' => 'Mundipagg\CreditCardController@storeCreditCard']);
        Route::post('creditcard/{id}', ['as' => 'creditcard.store', 'uses' => 'Mundipagg\CreditCardController@changeDefaultCreditCard']);

        Route::delete('creditcard/{id}', ['as' => 'creditcard.destroy', 'uses' => 'Mundipagg\CreditCardController@deleteCreditCard']);
    });

    /** Subscriber consumer */
    Route::get('get-subscribers-list', 'Api\LAConsumerController@subscriberList')->name('subscribers.get.list');
    Route::get('get-subscription-data', 'Api\LAConsumerController@courseList')->name('subscribers.get.subscriptions');
    Route::post('update-subscriber-last-access', 'Api\LAConsumerController@updateSubscriberLastAccess')->name('subscribers.update.subscriber.last.access');
    Route::post('update-subscriber-expo-la-token', 'Api\LAConsumerController@updateSubscriberExpoLAToken')->name('subscribers.update.subscriber.expo.la.token');
});

Route::post('eduzz/{integration_id}', ['uses' => 'EduzzController@principal']);
Route::post('hotmart/{integration_id}', ['uses' => 'HotmartController@principal']);
Route::post('plx/{integration_id}', ['uses' => 'PlxController@principal']);
Route::post('digitalmanagerguru/{integration_id}', ['uses' => 'DigitalManagerGuruController@principal']);


//API Checkout
Route::group(
    ['prefix' => 'checkout', 'middleware' => 'audit.route'],
    function () {
        //webhook confirm payment
        Route::post('order/paid', ['as' => 'mundipagg.order.paid', 'uses' => 'CheckoutApiController@webhookOrderPaid']);
        Route::post('order/paid/pagarme', ['as' => 'pagarme.order.paid', 'uses' => 'Pagarme\CheckoutOrderService@pagarmePostback']);

        //Dowload boleto
        Route::get('boleto/{order_id}', ['as' => 'checkout.boleto.download', 'uses' => 'CheckoutApiController@downloadBoleto']);
        Route::group(['middleware' => 'api.checkout.access'], function () {
            Route::post('installmentvalue', ['as' => 'checkout.installmentvalue', 'uses' => 'CheckoutApiController@getInstallmentValue']);
            Route::get('platforms', ['as' => 'checkout.platform.list', 'uses' => 'CheckoutApiController@listPlatforms']);
            Route::get('platforms/{platform_id}', ['as' => 'checkout.platform.get', 'uses' => 'CheckoutApiController@getPlatform']);
            Route::get('platforms/{platform_id}/plans', ['as' => 'checkout.plan.list', 'uses' => 'CheckoutApiController@listPlans']);
            Route::get('platforms/{platform_id}/plans/{plan_id}', ['as' => 'checkout.plan.get', 'uses' => 'CheckoutApiController@getPlan']);
            Route::post('subscriber', ['as' => 'checkout.subscriber.save', 'uses' => 'CheckoutApiController@saveSubscriber']);

            //Recebe obter id do usuário por middleware da mesma forma que api.checkout porém sem contar o número de tentativas
            Route::get('checkplan/{plan_id}', ['as' => 'mundipagg.checkout.checkPlan', 'uses' => 'CheckoutApiController@checkPlan']);

            Route::get('cupom/{cupom_code}', ['as' => 'mundipagg.checkout.cupom', 'uses' => 'CheckoutApiController@checkCupom']);

            //protected routes
            Route::group(['middleware' => 'api.checkout'], function () {
                Route::post('upsell/{platform_id}', ['as' => 'mundipagg.checkout.upsell', 'uses' => 'CheckoutApiController@upSell']);
                Route::post('{platform_id}/{plan_id}', ['as' => 'mundipagg.checkout.save', 'uses' => 'CheckoutApiController@checkout']);
            });
        });
    }
);

//API Checkout
Route::group(
    ['prefix' => 'callcenter', 'middleware' => 'callcenter.api'],
    function () {
        Route::get('deliver-leads', ['as' => 'callcenter.deliver-leads', 'uses' => 'CallCenterApiController@deliverLeads']);
        Route::get('get-leads', ['as' => 'callcenter.get-leads', 'uses' => 'CallCenterApiController@getLeads']);
        Route::get('resend-access-data', ['as' => 'callcenter.resend-access-data', 'uses' => 'CallCenterApiController@resendAccessData']);
        Route::get('change-card', ['as' => 'change-card', 'uses' => 'CallCenterApiController@changeCard']);
        Route::get('resend-boleto', ['as' => 'resend-boleto', 'uses' => 'CallCenterApiController@resendBoleto']);
        Route::get('link-pending', ['as' => 'link-pending', 'uses' => 'CallCenterApiController@linkPending']);
        Route::get('link-offer', ['as' => 'link-offer', 'uses' => 'CallCenterApiController@linkOffer']);
    }
);

//API Backoffice
Route::group(['prefix' => 'backoffice', 'middleware' => 'backoffice.api'], function () {
    Route::prefix('subscribers')->group(function () {
        Route::post('resend-data', [BackofficeController::class, 'resendSubscriberData'])->name('api.backoffice.resend.data');
    });
});

Route::prefix('webhooks')->group(function () {

    Route::middleware(['basicAuthPostmark'])->group(function () {
        Route::post('postmark', 'PostmarkController@bounceWebhook');
    });

    Route::prefix('provider1')->group(function () {
        Route::post('sms', 'Api\BandwidthSmsController@smsCallback')->name('bandwidth.sms-callback');

        Route::post('voice/test/{number}', 'Api\BandwidthVoiceController@voiceTest')->name('bandwidth.voice-test');

        Route::post('voice/init', 'Api\BandwidthVoiceController@voiceInitCallback')->name('bandwidth.voice-init-callback');
        Route::post('voice/status', 'Api\BandwidthVoiceController@voiceStatusCallback')->name('bandwidth.voice-status-callback');
        Route::post('voice/fallback', 'Api\BandwidthVoiceController@voiceInitFallback')->name('bandwidth.voice-init-fallback');
    });

    Route::prefix('tmb')->middleware(\App\Http\Middleware\TmbMiddleware::class)->group(function () {
        Route::post('events', [\App\Http\Controllers\Api\Webhooks\TmbController::class, '__invoke'])
            ->name('webhooks.tmb.events');
    });
});

include 'mobile.php';
