<?php

namespace App\Http\Controllers\Mundipagg;

use App\Plan;
use App\Services\Contracts\SubscriptionServiceInterface;
use Carbon\Carbon;
use Exception;
use App\Payment;
use App\Mail\SendMailRefund;
use Illuminate\Http\Request;
use App\Services\EmailService;
use App\Services\MundipaggService;
use App\Http\Controllers\Controller;
use App\Services\Mundipagg\SplitService;
use App\Utils\TriggerIntegrationJob;
use Illuminate\Support\Facades\Auth;
use MundiAPILib\Models\CreateCancelChargeRequest;
use MundiAPILib\Models\CreateSplitOptionsRequest;
use MundiAPILib\Models\CreateSplitRequest;

class SubscriberController extends Controller
{
    private $subscriptionService;

    public function __construct(SubscriptionServiceInterface $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    public function cancelCharge($platform_id, $payment_id, Request $request, $reason = null, $single = false) {
        $reason = $reason ?? $request->input('cancellationReason');
        if ($reason == null) throw new Exception('É obrigatório descrever o motivo do estorno.');
        if (strlen($reason) < 10 || strlen($reason) > 100) throw new Exception('O motivo deve ter entre 10 e 100 caracteres.');

        $mundipaggService = new MundipaggService($platform_id);
        $payment = Payment::findOrFail($payment_id);

        $payment->cancellation_reason = $reason;
        $payment->cancellation_at = Carbon::now();
        $payment->cancellation_user = Auth::user()->id;
        $payment->save();

        $cancelChargeRequest = new CreateCancelChargeRequest();
        $cancelChargeRequest->amount = str_replace('.','',(string) number_format($payment->price, 2, '.', '.'));

        $result = $mundipaggService->getClient()->getCharges()->cancelCharge($payment->charge_id);

        if( $result->status == 'canceled' && $single == false )
        {
            $payment->status = Payment::STATUS_CANCELED;
            $payment->save();

            $this->subscriptionService->cancelSubscriptionByPayment($payment);

            try {
                $subscriber = $payment->subscriber;
                
                config(['mail.tag' => $platform_id]);

                EmailService::mail(
                    [$subscriber->email],
                    new SendMailRefund($platform_id, $subscriber, $payment, $result->code, $payment->price, $payment->getTotalPlansValue())
                );
            }
            catch(Exception $e) {}
        }

        return response()->json($result);
    }

    private function getCancelChargeSplit(Payment $payment) {

        $splitService = new SplitService($payment->platform_id);
        $clientRecipientId = $splitService->getClientRecipient();
        $xgrowRecipientId = $splitService->getXgrowRecipient();

        //Utiliza valor do produto devido aos multiplos cartões, para estornar cada parte do valor
        $totalPlanValue = $payment->plans_value ?? $payment->getTotalPlansValue();

        $totalValue = ( $payment->installments > 0 ? $payment->installments : 1 )* Plan::getInstallmentValue($totalPlanValue, $payment->installments);
        $xGrowValue = $totalValue-$totalPlanValue;
        $clientValue = $totalPlanValue;

        $splitClient = new CreateSplitRequest();
        $splitClient->recipientId = $clientRecipientId;
        $splitClient->amount = str_replace('.','',(string) number_format($clientValue, 2, '.', '.'));
        $splitClient->type = "flat";
        $splitClient->options = new CreateSplitOptionsRequest();
        $splitClient->options->chargeRemainderFee = true;
        $splitClient->options->chargeProcessingFee = true;
        $splitClient->options->liable = true;
        $splits[] = $splitClient;

        if( $xGrowValue > 0 ) {
            $splitXgrow = new CreateSplitRequest();
            $splitXgrow->recipientId = $xgrowRecipientId;
            $splitXgrow->amount = str_replace('.','',(string) number_format($xGrowValue, 2, '.', '.'));
            $splitXgrow->type = "flat";
            $splitXgrow->options = new CreateSplitOptionsRequest();
            $splitXgrow->options->chargeRemainderFee = false;
            $splitXgrow->options->chargeProcessingFee = false;
            $splitXgrow->options->liable = false;
            $splits[] = $splitXgrow;
        }

        return $splits;
    }

}
