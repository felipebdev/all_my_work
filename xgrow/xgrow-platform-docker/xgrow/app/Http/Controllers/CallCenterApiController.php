<?php

namespace App\Http\Controllers;

use App\Attendant;
use App\Email;
use App\EmailPlatform;
use App\Attendance;
use App\CreditCard;
use App\Http\Controllers\SubscriberController;
use App\Mail\SendMailChangeCard;
use App\Mail\SendMailBankSlip;
use App\Mail\SendLinkPending;
use App\Mail\SendLinkOffer;
use App\Payment;
use App\Services\Callcenter\CallcenterService;
use App\Services\Contracts\JwtPlatformServiceInterface;
use App\Services\EmailService;
use App\Subscriber;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CallCenterApiController extends Controller
{
    private $callCenterService;
    private $subscriber;
    private $payment;
    private $attendance;

    public function __construct(CallcenterService $callCenterService, Subscriber $subscriber, EmailService $emailService, Payment $payment, Attendance $attendance)
    {
        $this->callCenterService = $callCenterService;
        $this->subscriber = $subscriber;
        $this->emailService = $emailService;
        $this->payment = $payment;
        $this->attendance = $attendance;
    }


    public function deliverLeads(Request $request)
    {
        try {

            $uuid = $request->uuid;

            $attendant = Attendant::where('uuid', $uuid)->first();

            if($attendant){
                $this->callCenterService->deliverLeadsByAttendant($attendant->id);

                return response()->json(['status' => 'success']);
            }

             return response()->json(['response' => 'error', 'message' => 'Atendente não localizado: ' . $request->uuid],500);


        } catch (Exception $e) {
            return response()->json(['response' => 'fail', 'message' => $e->getMessage()],500);
        }
    }

    public function getLeads(Request $request)
    {
        try {

            $attendant = Attendant::where('uuid', $request->uuid)->first();

            if(!$attendant)
                return response()->json([], 204);

            $leads = $attendant->subscribers()->get();

            return response()->json([
                'status' => 'success',
                'leads' => $leads,
            ]);

        } catch (Exception $e) {
            return response()->json(['response' => 'fail', 'message' => $e->getMessage()],500);
        }
    }

    public function resendAccessData(Request $request)
    {
        try {

            $subscriber = $this->subscriber->find($request->subscriber_id);
            if ($subscriber === null) {
                return response()->json(['status' => 'error', 'message' => 'Assinante não foi encontrado!']);
            }

            $ret = $this->emailService->sendMailNewRegisterSubscriber($subscriber);

            if (!$ret) {
                return response()->json(['status' => 'error', 'message' => 'Plano desse assinante não está habilitado para envio de e-mail!', 'ret' => $ret]);
            }

            return response()->json(['status' => 'success', 'message' => "Dados enviados com sucesso!", 'ret' => $ret]);

        } catch (Exception $e) {
            return response()->json(['response' => 'fail', 'message' => $e->getMessage()],500);
        }
    }
    public function resendBoleto(Request $request)
    {
        try {
            $payment = $this->payment->find($request->payment_id);

            if(!$payment)
                return response()->json(['status' => 'error', 'message' => 'Nenhum pagamento localizado']);

            if(!$this->checkEmailRegister($request->platform_id, Email::CONSTANT_EMAIL_BOLETO))
                return response()->json(['status' => 'error', 'message' => 'Mensagem de email não cadastrada pelo produtor!']);

            $subscriber = $payment->subscriber;
            EmailService::mail(
                [$subscriber->email],
                new SendMailBankSlip($request->platform_id, $subscriber, $payment)
            );

            return response()->json(['status' => 'success', 'message' => 'Boleto reenviado com sucesso!']);


        } catch (Exception $e) {

            return response()->json(['response' => 'fail', 'message' => $e->getMessage()],500);
        }
    }
    public function linkPending(Request $request)
    {
        try {

            $attendance = $this->attendance->withoutGlobalScopes()->find($request->attendance_id);

            $url = $attendance->audience->action->link_pending;

            if(!$url){
                return response()->json(['status' => 'error', 'message' => 'Nenhum link informado']);
            }

            if(!$this->checkEmailRegister($request->platform_id, Email::CONSTANT_EMAIL_LINK_PENDING)){
                return response()->json(['status' => 'error', 'message' => 'Mensagem de email não cadastrada pelo produtor!']);
            }

            EmailService::mail(
                    [$attendance->subscriber->email],
                    new SendLinkPending($request->platform_id, $attendance->subscriber, $url)
                );
            return response()->json(['status' => 'success', 'message' => 'Pagamento enviado com sucesso!']);


        } catch (Exception $e) {

            return response()->json(['response' => 'fail', 'message' => $e->getMessage()],500);
        }
    }

    public function linkOffer(Request $request)
    {
        try {

            $attendance = $this->attendance->withoutGlobalScopes()->find($request->attendance_id);

            $url = $attendance->audience->action->link_offer;

            if(!$url){
                return response()->json(['status' => 'error', 'message' => 'Nenhum link informado']);
            }

            if(!$this->checkEmailRegister($request->platform_id, Email::CONSTANT_EMAIL_LINK_OFFER)){
                return response()->json(['status' => 'error', 'message' => 'Mensagem de email não cadastrada pelo produtor!']);
            }

           EmailService::mail(
                [$attendance->subscriber->email],
                new SendLinkOffer($request->platform_id, $attendance->subscriber, $url)
            );

            return response()->json(['status' => 'success', 'message' => 'Link de produto enviado com sucesso!']);



        } catch (Exception $e) {

            return response()->json(['response' => 'fail', 'message' => $e->getMessage()],500);
        }
    }

    public function changeCard(Request $request, JwtPlatformServiceInterface $jwtPlatformService)
    {
        try {

            $platformId = $request->platform_id;

            $subscriber = $this->subscriber->select(
                'subscribers.name',
                'subscribers.document_number',
                'subscribers.email',
                'subscribers.platform_id'
            )
                ->where('platform_id', $platformId)
                ->where('id', '=', $request->subscriber_id)
                ->firstOrFail();

            if(!$this->checkEmailRegister($platformId, Email::CONSTANT_EMAIL_CHANGE_CARD)){
                return response()->json(['status' => 'error', 'message' => 'Mensagem de email não cadastrada pelo produtor!']);
            }

            // Get subscriber credit cards
            $cards = CreditCard::where('subscriber_id', '=', $request->subscriber_id)->get();

            if($cards->count() == 0){
                return response()->json(['status' => 'error', 'message' => 'Nenhum cartão registrado para esse aluno']);
            }

            $token = $jwtPlatformService->generateToken($platformId, $subscriber->email, $subscriber->document_number ?? '');

            $baseUrl = env('APP_URL_SETTINGS', 'https://settings.xgrow.com');
            $urlWithToken = "$baseUrl/{$platformId}/dashboard?token={$token}";

            try {
                EmailService::mail([$subscriber->email], new SendMailChangeCard($platformId, $subscriber, $urlWithToken));
            } catch (Exception $e) {
                report($e);
                return response()->json(['status' => 'error', 'message' => 'Falha ao enviar email, favor entrar em contato com suporte']);
            }

            return response()->json(['status' => 'success', 'message' => "Dados de troca do cartão enviado!"]);

        } catch (ModelNotFoundException $e) {
            return response()->json(['status' => 'error', 'message' => 'Aluno não encontrado']);
        } catch (Exception $e) {
            report($e);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }


    private function checkEmailRegister($platform_id, $email_id){
        return EmailPlatform::where('platform_id', $platform_id)->where('email_id', $email_id)->first();
    }


}
