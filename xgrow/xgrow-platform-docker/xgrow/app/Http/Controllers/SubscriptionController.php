<?php

namespace App\Http\Controllers;

use App\Payment;
use App\Services\Contracts\SubscriptionServiceInterface;
use App\Subscription;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\LA\CacheClearService;

class SubscriptionController extends Controller
{
    private $subscriptionService;
    private CacheClearService $cacheClearService;

    public function __construct(SubscriptionServiceInterface $subscriptionService, CacheClearService $cacheClearService)
    {
        $this->subscriptionService = $subscriptionService;
        $this->cacheClearService = $cacheClearService;
    }

    public function cancel(Request $request)
    {
        $return = false;
        if (isset($request->id)) {
            $subscription = Subscription::findOrFail($request->id);
            $payment = Payment::where('order_code', '=', $subscription->gateway_transaction_id)->where('platform_id', '=', Auth::user()->platform_id)->first();
            if ($payment) {
                $recurrence = $payment->recurrences->first();
                $dateCancel = new Carbon($recurrence->last_payment);
                $subscription->status = Subscription::STATUS_CANCELED;
                $subscription->status_updated_at = \Carbon\Carbon::now();
                $subscription->canceled_at = $dateCancel->addDays($recurrence->recurrence);
                $return = $subscription->save();
            }
            $this->cacheClearService->clearSubscriberCache(Auth::user()->platform_id, null, $subscription->subscriber->id);
        }
        return response()->json($return);
    }

    public function cancelNotRefund(Subscription $subscription, Request $request)
    {
        $payments = $subscription->payments;
        $firstPayment = $payments->first() ?? null;
        $isUnlimited = $firstPayment->type == Payment::TYPE_UNLIMITED;

        try {
            if ($isUnlimited) {
                $this->subscriptionService->cancelSubscriptionsAndPayments(
                    $subscription->order_number,
                    'Cancelado manualmente',
                    Auth::user()->id
                );
            } else {
                $this->subscriptionService->cancel($subscription, $request->input('canceled_at'));
            }

            $this->cacheClearService->clearSubscriberCache(Auth::user()->platform_id, null, $subscription->subscriber->id);
            return redirect()
                ->back()
                ->withSuccess("O cancelamento do produto {$subscription->plan->name} do aluno {$subscription->subscriber->name} foi realizado com sucesso!");
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withErrors("Um erro ocorreu e não foi possível realizar o cancelamento do produto {$subscription->plan->name} do aluno {$subscription->subscriber->name}.");
        }
    }

    /**
     * @deprecated use \App\Services\Checkout\RefundService::refund instead
     */
    public function cancelRefund(Subscription $subscription)
    {
        try {
            $this->subscriptionService->cancelRefund($subscription);
            $this->cacheClearService->clearSubscriberCache(Auth::user()->platform_id, null, $subscription->subscriber->id);
            return redirect()
                ->back()
                ->withSuccess("O cancelamento do produto {$subscription->plan->name} do aluno {$subscription->subscriber->name} e o estorno dos pagamentos foi realizado com sucesso!");
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withErrors("Um erro ocorreu e não foi possível realizar o cancelamento do produto {$subscription->plan->name} do aluno {$subscription->subscriber->name} e o estorno dos pagamentos.");
        }
    }

    /**
     * @deprecated use \App\Services\Checkout\RefundService::refund instead
     */
    public function cancelRefundPix(Subscription $subscription)
    {
        try {
            $this->subscriptionService->cancelRefundPix($subscription);
            $this->cacheClearService->clearSubscriberCache(Auth::user()->platform_id, null, $subscription->subscriber->id);
            return redirect()
                ->back()
                ->withSuccess("O cancelamento do produto {$subscription->plan->name} do aluno {$subscription->subscriber->name} e o estorno dos pagamentos foi realizado com sucesso!");
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withErrors("Um erro ocorreu e não foi possível realizar o cancelamento do produto {$subscription->plan->name} do aluno {$subscription->subscriber->name} e o estorno dos pagamentos.");
        }
    }

    /**
     * @deprecated use \App\Services\Checkout\RefundService::refund instead
     */
    public function cancelRefundBoleto(Subscription $subscription, Request $request)
    {
        try {
            $this->subscriptionService->cancelRefundBoleto(
                $subscription,
                $request->bankCode,
                $request->agency,
                $request->agencyDigit,
                $request->account,
                $request->accountDigit,
                $request->documentNumber,
                $request->legalName
            );
            $this->cacheClearService->clearSubscriberCache(Auth::user()->platform_id, null, $subscription->subscriber->id);
            return redirect()
                ->back()
                ->withSuccess("O cancelamento do produto {$subscription->plan->name} do aluno {$subscription->subscriber->name} e o estorno dos pagamentos foi realizado com sucesso!");
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withErrors("Um erro ocorreu e não foi possível realizar o cancelamento do produto {$subscription->plan->name} do aluno {$subscription->subscriber->name} e o estorno dos pagamentos.");
        }
    }

    public function subscriberManualAdd(Request $request)
    {
        try {

            if(in_array(null, $request->input(['plans']))){
                throw new Exception('Informe o produto');
            }

            foreach ($request->input(['plans']) as $plan) {

                    Subscription::insert([
                        'platform_id' => Auth::user()->platform_id,
                        'plan_id' => $plan,
                        'subscriber_id' => $request->input('subscriber_id'),
                        'created_at' => date('Y-m-d H:i:s'),
                        'status' => Subscription::STATUS_ACTIVE,
                        'status_updated_at' => now(),
                    ]);

                $this->cacheClearService->clearSubscriberCache(Auth::user()->platform_id, null, $request->input('subscriber_id'));
            }

            return response()->json([
                'data' => 'Produto(s) cadastrado(s) com sucesso!',
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => true, 'response' => $e->getMessage()], 500);
        }
    }


    public function cancelOrderNumber(Request $request, $order_number)
    {
        $payments = Payment::where('platform_id', Auth::user()->platform_id)
            ->where('order_number', $order_number)->get();
        if (count($payments) <= 0) {
            return response()->json(['error' => true, 'response' => 'Pagamento não encontrado'], 404);
        }

        $result = $this->subscriptionService->cancelSubscriptionsAndPayments($order_number);

        if ($result) {
            return response()->json([
                'data' => 'Cancelamento realizado com sucesso',
            ]);
        }
        return response()->json(['error' => true, 'response' => 'Falha ao realizar cancelamento'], 500);
    }

    public function changeSubscriptionStatus(Request $request)
    {
        try {
            $status = $request->input('sub_status');
            $subscription = Subscription::findOrFail($request->input('sub_id'));

            if ($status === 'active') {
                $subscription->canceled_at = null;
                $subscription->payment_pendent = null;
            }

            if ($status === 'canceled') {
                $subscription->canceled_at = Carbon::now();
            }

            if ($status === 'pending') {
                $subscription->payment_pendent = Carbon::now();
                $subscription->canceled_at = null;
            }

            $subscription->status = $request->input('sub_status');
            $subscription->save();
            $this->cacheClearService->clearSubscriberCache($subscription->platform_id, null, $subscription->subscriber_id);
            return response()->json(['error' => false, 'message' => 'Produto alterado com sucesso!']);
        } catch (Exception $e) {
            return response()->json(['error' => true, 'response' => $e->getMessage()], 400);
        }
    }
}
