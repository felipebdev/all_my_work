<?php

namespace App\Services;

use App\Mail\SendMailPaymentConfirmed;
use Hash;
use Config;
use App\Plan;
use App\Email;
use Exception;
use App\Platform;
use App\Mail\BaseMail;
use App\Mail\SendMailAuto;
// use App\Services\EmailService;
use App\Mail\SendMailPurchaseProof;
use App\Mail\SendMailLinkCallcenter;
use App\Http\Controllers\Controller;
use App\Logs\XgrowLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Services\LA\CacheClearService;



class EmailService extends Controller
{
    private $plan;
    private CacheClearService $cacheClearService;

    public function __construct()
    {
        $this->plan = new Plan;
        $this->cacheClearService = app()->make(CacheClearService::class);
    }

    public function sendMailNewRegisterSubscriber($subscriber, $password = null, $forceSend = 0)
    {
        $plan = $this->plan->find($subscriber->plan_id);

        $trigger_email = 1;
        if( isset($plan) ) {
            $trigger_email = $plan->trigger_email;
        }
        $ret = false;
        if ( $trigger_email === 1 or $forceSend == 1) {

            if (empty($password)) {
                $password = keygen(12);
            }

            $subscriber->update([
                'raw_password' => $password
            ]);

            $emailData = [
                'subscriber' => true,
                'password' => $password,
                'platform_id' => $subscriber->platform_id,
                'user' => $subscriber,
                'plan_name' => $plan->name ?? ' - ',
                'email_id' => Email::CONSTANT_EMAIL_NEW_REGISTER,
                'subscriber_model' => $subscriber
            ];

            $usersTo = [$subscriber->email];

            self::send($usersTo, new SendMailAuto($emailData));

            $config = ['name' => 'Xgrow'];

            Config::set('app', $config);
            $ret = true;

            $this->cacheClearService->clearSubscriberCache($subscriber->platform_id, $subscriber->email, $subscriber->id);
        }
        return $ret;
    }

    public function sendMailCodeAction($user, $code)
    {
        $ret = false;
        $emailData = [
            'subscriber' => false,
            'platform_id' => $user->platform_id,
            'user' => $user,
            'email_id' => Email::CONSTANT_EMAIL_ACTION_CODE,
            'code' => $code
        ];
        $usersTo = [$user->email];
        self::send($usersTo, new SendMailAuto($emailData));
        $config = ['name' => 'Xgrow'];
        Config::set('app', $config);
        $ret = true;
        return $ret;
    }

    public function sendMailPurchaseProofAfterCheckout($platform, $subscriber, $payment)
    {
        $password = keygen(12);
        $subscriber->update(['raw_password' => $password]);

        $return = false;
        try {
            //Send payment confirmation mail
            EmailService::mail(
                [$subscriber->email],
                new SendMailPaymentConfirmed($platform->id, $subscriber, $payment)
            );

            //Check use internal learning area
            if( $payment->plans()->first()->product->internal_learning_area == true ) {
                //Send
                EmailService::mail(
                    [$subscriber->email],
                    new SendMailPurchaseProof($platform->id, $subscriber, $payment, $password)
                );
            }

            $return = true;
        } catch (Exception $e) {
        }

        return $return;
    }

    public function sendMailLinkCallcenter($attendant)
    {


        $return = false;
        try {
            EmailService::mail(
                [$attendant->email],
                new SendMailLinkCallcenter($attendant)
            );
            $return = true;
        } catch (Exception $e) {
        }

        return $return;
    }

    /**
     * Check platform domain and send e-mail
     * @param array $recipient
     * @param SendMailAuto $sendMailAuto
     */
    public static function send(array $recipient, SendMailAuto $sendMailAuto)
    {
        //Gets the domain name for Mailgun if Reply to (name and email) are filled
        $platform = Platform::find($sendMailAuto->getDataUser()->platform_id);
        if (strlen($platform->reply_to_email) > 0 && strlen($platform->reply_to_name) > 0) {
            $explodeUrl = explode('.', $platform->url_official);
            $domain = implode('.', array_splice($explodeUrl, 1));
            Config::set('services.mailgun.domain', $domain);
        }
        Mail::to($recipient)->send($sendMailAuto);
        XgrowLog::mail()->debug('>', [
            'platform' => [
                'id' => $platform->id,
                'name' => $platform->name,
            ],
            'subject' => $sendMailAuto->subject,
            'recipients' => $recipient
        ]);
    }

    public static function mail(array $recipient, BaseMail $mail)
    {
        try {
            Mail::to($recipient)->send($mail);
            if (count(Mail::failures()) > 0) {
                throw new Exception();
            }

            XgrowLog::mail()->debug('>', [
                'platform' => [
                    'id' => $mail->platform->id,
                    'name' => $mail->platform->name,
                ],
                'subject' => $mail->subject,
                'recipients' => $recipient
            ]);
        } catch (Exception $e) {
            throw $e;
        }
    }
}
