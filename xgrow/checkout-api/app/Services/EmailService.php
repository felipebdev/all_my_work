<?php

namespace App\Services;

use App\Email;
use App\Facades\Whatsapp;
use App\Http\Controllers\Controller;
use App\Jobs\SendSmsAccessDataJob;
use App\Logs\XgrowLog;
use App\Mail\BaseMail;
use App\Mail\SendMailAuto;
use App\Mail\SendMailLinkCallcenter;
use App\Mail\SendMailPaymentConfirmed;
use App\Mail\SendMailPurchaseProof;
use App\Payment;
use App\Plan;
use App\Platform;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailService extends Controller
{
    private $plan;

    public function __construct()
    {
        $this->plan = new Plan;
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
                'password' => Hash::make($password)
            ]);

            $emailData = [
                'subscriber' => true,
                'password' => $password,
                'platform_id' => $subscriber->platform_id,
                'user' => $subscriber,
                'plan_name' => $plan->name ?? ' - ',
                'email_id' => Email::CONSTANT_EMAIL_NEW_REGISTER
            ];

            $usersTo = [$subscriber->email];

            self::send($usersTo, new SendMailAuto($emailData));

            $config = ['name' => 'Xgrow'];

            Config::set('app', $config);
            $ret = true;
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

    /**
     * Send Payment Confirmed email, and (if needed) login access
     *
     * @param $platform
     * @param $subscriber
     * @param  \App\Payment  ...$payments
     * @return bool True if correctly processed, false otherwise
     */
    public function sendMailPurchaseProofAfterCheckout($platform, $subscriber, Payment  ...$payments): bool
    {
        try {
            //Send payment confirmation mail
            $mailPaymentConfirmed = new SendMailPaymentConfirmed($platform->id, $subscriber, ...$payments);

            EmailService::mail([$subscriber->email], $mailPaymentConfirmed );

            $firstPayment = $payments[0];

            if ($firstPayment->plans()->first()->pivot->type === 'upsell') {
                return true; // skip login access on upsell
            }

            //Check use internal learning area
            if ($firstPayment->plans()->first()->product->internal_learning_area != true) {
                return true;
            }

            $password = 'Utilize sua senha atual.';
            if(strlen($subscriber->password) == 0){
                $password = keygen(12);
                $subscriber->update(['password' => Hash::make($password)]);
            }

            if ($platform->notifications_whatsapp ?? false) {
                Whatsapp::paymentConfirmed($firstPayment, $subscriber->email, $password);
            }

            $mailPurchaseProof = new SendMailPurchaseProof($platform->id, $subscriber, $firstPayment, $password);

            EmailService::mail([$subscriber->email], $mailPurchaseProof);

            //FIXME Envia SMS com Dados de acesso do Pior ano da Sua vida 2023. Remover após lançamento
            if( $platform->id == '89d6084b-99ae-481c-8646-05c99c98b469' ) {
                SendSmsAccessDataJob::dispatch(Carbon::now()->toISOString(), $platform->id, $subscriber, env('BITLY_TOKEN'), false, $password);
            }

            return true;
        } catch (Exception $e) {
            Log::error("[SEND MAIL PURCHASE PROOF] - ".$e->getMessage(), $e->getTrace());
        }

        return false;
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
