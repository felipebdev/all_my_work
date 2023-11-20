<?php

namespace App\Http\Controllers\Getnet;

use App\GetnetCharge;
use App\Integration;
use App\IntegrationType;
use App\Payment;
use App\Platform;
use App\Services\Getnet\ClientService as GetnetClientService;
use App\Services\Getnet\SubscriptionService;
use Hash;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Services\Getnet\SubscriptionService as GetnetSubscriptionService;
use App\Subscription;
use App\Subscriber;
use App\Plan;
use App\Safe;
use App\Constants;
use StdClass;
use DB;
use Yajra\DataTables\DataTables;

class SubscriptionController extends Controller
{
    private $getnetSubscriptionService;
    private $dataTable;
    private $payment;

    public function __construct(Datatables $dataTable, Payment $payment)
    {

        $this->dataTable = $dataTable;
        $this->payment = $payment;

        $this->middleware(['auth']);
        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            if (verifyIntegration('GETNET',  $user->platform_id)) {
                $this->getnetSubscriptionService = new GetnetSubscriptionService($user->platform_id);
            }

            return $next($request);
        });

    }

    public function index()
    {
        $subscriptions = $this->getnetSubscriptionService->index();

        return view('getnet.subscriptions.index', compact('subscriptions'));
    }

    public function getSubscription($subscriptionId)
    {
        $subscription = $this->getnetSubscriptionService->getSubscription($subscriptionId);
        $projection = $this->getnetSubscriptionService->getProjection($subscriptionId);

        return view('getnet.subscriptions.edit', compact('subscription', 'projection'));
    }

    public function cancelSubscription(Request $request)
    {
        $subscription = IntegrationType::where('integration_type_id', $request->subscription_id)
            ->where('integratable_type', 'App\\Subscription')
            ->first();

        if ($subscription != null) {
            $request->request->add(['id' => $subscription->integratable_id]);
        }

        $subscription = $this->getnetSubscriptionService->cancelSubscription($request);

        if ($subscription['status'] === 'error') {
            if ($request->returnJson) {
                return response()->json([
                    'status' => 'error',
                    'message' => $subscription['data']['response']
                ]);
            }
            return back()->withErrors(['message' => $subscription['data']['response']]);
        }

        if (isset($request->id)) {
            $subscription = Subscription::find($request->id);
            $subscription->update([
                'status' => Subscription::STATUS_CANCELED,
                'status_updated_at' => now(),
                'canceled_at' => now()
            ]);
        }

        if ($request->returnJson) {
            return response()->json([
                'status' => 'success',
                'cancelDate' => date_format($subscription->canceled_at, 'd/m/Y H:i:s'),
                'message' => 'Assinatura "' . $request->subscription_id . '" cancelada com sucesso!'
            ]);
        }

        return redirect('/getnet/subscriptions')->with(['message' => 'Assinatura "' . $request->subscription_id . '" cancelada com sucesso!']);
    }

    public function paymentDateSubscription(Request $request)
    {
        $subscription = $this->getnetSubscriptionService->paymentDateSubscription($request);

        if ($subscription['status'] === 'error') {
            response()->json(['status' => 'error', 'data' => $subscription, 'message' => $subscription['data']['response']]);
        }

        return response()->json(['status' => 'success', 'data' => $subscription, 'message' => 'Data de pagamento atualizada com sucesso!']);
    }

    public function paymentTypeCreditCardSubscription(Request $request)
    {
        $subscription = $this->getnetSubscriptionService->paymentTypeCreditCardSubscription($request);

        if ($subscription['status'] === 'error') {
            response()->json(['status' => 'error', 'data' => $subscription, 'message' => $subscription['data']['response']]);
        }

        return response()->json(['status' => 'success', 'data' => $subscription, 'message' => 'Data de pagamento atualizada com sucesso!']);
    }

    public function integrateAll()
    {
        $platforms = Platform::whereNull('deleted_at')->get();

        if(!$platforms) {
            dd('Fim sem registros');
        }

        foreach ($platforms as $platform) {
            $platform_id = $platform->id;

            if (verifyIntegration('GETNET', $platform_id)) {
                $subscriptions = Subscription::where('subscriptions.platform_id', '=', $platform_id)
                    ->join('plans', 'plans.id', '=', 'subscriptions.plan_id')
                    ->whereNull('subscriptions.canceled_at')
                    ->where('plans.status', '=', 1)
                    ->where(DB::raw("SUBSTRING(DATE_ADD(subscriptions.created_at, INTERVAL plans.freedays DAY),1,10)"), '<=', DB::raw("SUBSTRING(NOW(), 1, 10)"))
                    ->where('plans.type_plan', 'R')
                    ->select('subscriptions.id', 'subscriptions.subscriber_id', 'subscriptions.plan_id', 'subscriptions.created_at',DB::raw('SUBSTRING(DATE_ADD(subscriptions.created_at, INTERVAL plans.freedays DAY),1,10) AS when_charge'), 'subscriptions.gateway_transaction_id', 'plans.recurrence', 'plans.charge_until', 'plans.price')
                    ->orderby('subscriptions.subscriber_id')
                    ->limit(100)
                    ->get();

                if ($subscriptions && $subscriptions->count() > 0) {

                    $cont = $errors = $success = 0;

                    foreach ($subscriptions as $subscription) {

                        $safe = Safe::where('subscriber_id', '=', $subscription->subscriber_id)->where('platform_id', '=', $platform_id)->first();

                        if ($safe === null) {
                            continue;
                        }

                        $plan = Plan::find($subscription->plan_id);
                        $subscriber = Subscriber::find($subscription->subscriber_id);

                        $integration = Integration::where('id_webhook', '=', Constants::getKeyIntegration('GETNET'))->first();

                        $subscriptionIntegratable = $subscription->integratable->where('integration_id', '=', $integration->id)->first();

                        if ($subscriptionIntegratable === null) {
                            $cont++;

                            $dados = new StdClass;


                            $dados->plan_integration = isset($plan->integratable->where('integration_id', '=', $integration->id)->first()->integration_type_id) ?? false;
							if(!$dados->plan_integration){
								continue;
							}

							$dados->subscriber_integration = isset($subscriber->integratable->where('integration_id', '=', $integration->id)->first()->integration_type_id) ?? false;
							if(!$dados->subscriber_integration){
								continue;
							}

                            $dados->safe_integration = $safe->integration->integration_type_id;

                            $res = $this->getnetSubscriptionService->store($dados, $subscription);

                            if ($res['status'] != 'error') {
                                $subscription->integratable()->delete();
                                $integration = Integration::where('platform_Id', $subscriber->platform_id)->where('id_integration', '=', Constants::CONSTANT_INTEGRATION_GETNET)->first();
                                $subscription->integratable()->create(['integration_id' => $integration->id, 'integration_type_id' => $res['data']->subscription->subscription_id]);

                                // create payments on table payments
                                if ($subscription->charge_until > 0) {
                                    $date = $subscription->created_at;

                                    for ($i = 0; $i <= $subscription->charge_until; $i++) {

                                        $newDate = date('Y-m-d', strtotime('+'.$subscription->recurrence.' days', strtotime($date)));

                                        $date = $newDate;

                                        Payment::create([
                                            'subscription_id' => $subscription->id,
                                            'platform_id' => $platform_id,
                                            'price' => $subscription->price,
                                            'payment_data' => $newDate,
                                            'status' => 'schedule',
                                            'id_webhook' => Constants::getKeyIntegration('GETNET')
                                        ]);
                                    }
                                }
                            }

                            $response[] = $res;

                            ($res['status'] === 'error') ? $errors++ : $success++;
                        }
                    }

                    $response['total'] = $cont;
                    $response['success'] = $success;
                    $response['errors'] = $errors;
                    dd($response);
                }
                dd('Fim sem registros');
            }
        }
    }

    public function paymentsData()
    {
        // listagem anterior
//        $query = $this->payment::join('subscriptions', 'payments.subscription_id', '=', 'subscriptions.id')
//            ->join('subscribers', 'subscriptions.subscriber_id', '=', 'subscribers.id')
//            ->join('integrations', 'payments.id_webhook', '=', 'integrations.id_webhook')
//            ->join('integration_types', 'subscriptions.id' ,'=', 'integration_types.integratable_id')
//            ->where('payments.platform_id', '=', Auth::user()->platform_id)
//            ->select('payments.*', 'subscribers.name', 'integrations.name_integration', 'subscriptions.id','integration_types.integration_type_id');

//        $payments = Subscription::join('integration_types', 'subscriptions.gateway_transaction_id', '=', 'integration_types.integration_type_id')
//            ->join('getnet_charges', 'getnet_charges.subscription_id', '=', 'integration_types.integration_type_id')
//            ->join('plans', 'subscriptions.plan_id', '=', 'plans.id')
//            ->join('subscribers', 'subscribers.id', '=', 'subscriptions.subscriber_id')
//            ->where('subscriptions.platform_id', '=', Auth::user()->platform_id)
//            ->select('getnet_charges.charge_id AS id', 'getnet_charges.payment_date', 'getnet_charges.payment_type', 'plans.name', 'plans.price', 'getnet_charges.status', 'subscribers.name');

//        $payments = Payment::join('subscriptions', 'payments.subscription_id', '=', 'subscriptions.id')
//            ->join('subscribers', 'subscribers.id', '=', 'subscriptions.subscriber_id')
//            ->join('integrations', 'integrations.id_webhook', '=', 'payments.id_webhook')
//            ->join('integration_types', 'subscriptions.gateway_transaction_id', '=', 'integration_types.integration_type_id')
//            ->join('plans', 'subscriptions.plan_id', '=', 'plans.id')
//            ->leftJoin('getnet_charges', 'getnet_charges.subscription_id', '=', 'integration_types.integration_type_id')
//            ->where('subscriptions.platform_id', '=', Auth::user()->platform_id)
//            ->select('getnet_charges.charge_id AS id', 'getnet_charges.payment_date', 'getnet_charges.payment_type', 'plans.name', 'plans.price', 'getnet_charges.status', 'subscribers.name', 'integrations.name_integration');
//
//        return $this->dataTable->eloquent($payments)->make(true);
    }

    public function payments()
    {

//        $payments = Payment::join('subscriptions', 'payments.subscription_id', '=', 'subscriptions.id')
//            ->join('subscribers', 'subscriptions.subscriber_id', '=', 'subscribers.id')
//            ->join('integrations', 'payments.id_webhook', '=', 'integrations.id_webhook')
//            ->where('payments.platform_id', '=', Auth::user()->platform_id)
//            ->select('payments.*', 'subscribers.name', 'integrations.name_integration');
//
//        return $this->dataTable->eloquent($payments)->make(true);

//        listagem anterior
//        $payments = Payment::join('subscriptions', 'payments.subscription_id', '=', 'subscriptions.id')
//            ->join('subscribers', 'subscriptions.subscriber_id', '=', 'subscribers.id')
//            ->join('integrations', 'payments.id_webhook', '=', 'integrations.id_webhook')
//            ->join('integration_types', 'subscriptions.id' ,'=', 'integration_types.integratable_id')
//            ->where('payments.platform_id', '=', Auth::user()->platform_id)
//            ->select('payments.*', 'subscribers.name', 'integrations.name_integration', 'subscriptions.id','integration_types.integration_type_id')
//            ->get();

//
//        $payments = Payment::join('subscriptions', 'payments.subscription_id', '=', 'subscriptions.id')
//            ->join('subscribers', 'subscribers.id', '=', 'subscriptions.subscriber_id')
//            ->join('integrations', 'integrations.id_webhook', '=', 'payments.id_webhook')
//            ->join('integration_types', 'subscriptions.gateway_transaction_id', '=', 'integration_types.integration_type_id')
//            ->join('plans', 'subscriptions.plan_id', '=', 'plans.id')
//            ->leftJoin('getnet_charges', 'getnet_charges.subscription_id', '=', 'integration_types.integration_type_id')
//            ->where('subscriptions.platform_id', '=', Auth::user()->platform_id)
//            ->select('getnet_charges.charge_id AS id', 'getnet_charges.payment_date', 'getnet_charges.payment_type', 'plans.name', 'plans.price', 'getnet_charges.status', 'subscribers.name', 'integrations.name_integration')
//            ->get();


//        $payments = Subscription::join('integration_types', 'subscriptions.gateway_transaction_id', '=', 'integration_types.integration_type_id')
//            ->join('getnet_charges', 'getnet_charges.subscription_id', '=', 'integration_types.integration_type_id')
//            ->join('plans', 'subscriptions.plan_id', '=', 'plans.id')
//            ->join('subscribers', 'subscribers.id', '=', 'subscriptions.subscriber_id')
//            ->join('integrations', 'integrations.id_webhook', )
//            ->where('subscriptions.platform_id', '=', Auth::user()->platform_id)
//            ->select('getnet_charges.charge_id AS id', 'getnet_charges.payment_date', 'getnet_charges.payment_type', 'plans.name', 'plans.price', 'getnet_charges.status', 'subscribers.name')->get();

//        return view('integracao.payments', compact('payments'));
    }

    public function editPayment($id)
    {
        $payment = Payment::find($id);

        return view('integracao.payment-edit', compact('payment'));
    }

    public function importCharges()
    {
        $platforms = Platform::join('integrations', 'integrations.platform_id', '=', 'platforms.id')
            ->where('platforms.deleted_at', null)
            ->where('integrations.id_webhook', 4)
            ->select('platforms.id AS platform_id', 'integrations.id_webhook')
            ->get();

        if ($platforms->count() > 0 ) {

            foreach($platforms as $platform) {

                $getnetSubscriptionService = new SubscriptionService($platform->platform_id);

                $params = ['page' => 1, 'limit' => 500];
                $callback = $getnetSubscriptionService->importCharges($params);

                if(count($callback->charges) > 0) {
                    $this->recordCharges($callback->charges);
                }

                $calls = (int) ceil($callback->total / 500);

                if($calls > 1) {
                    for ($i = 2; $i <= $calls; $i++) {
                        $params = ['page' => $i, 'limit' => 500];
                        $callback = $getnetSubscriptionService->importCharges($params);

                        if(count($callback->charges) > 0) {
                            $this->recordCharges($callback->charges);
                        }
                    }
                }
            }
        }


    }

    public function recordCharges($charges)
    {
        foreach($charges as $charge){

            $data["amount"] = substr($charge->amount, 0, -2) . '.' . substr($charge->amount, -2) ;
            $data["charge_id"] = $charge->charge_id;
            $data["seller_id"] = $charge->seller_id;
            $data["subscription_id"] = $charge->subscription_id;
            $data["customer_id"] = $charge->customer_id;
            $data["plan_id"] = $charge->plan_id;
            $data["payment_id"] = $charge->payment_id;
            $data["status"] = $charge->status;
            $data["scheduled_date"] = $charge->scheduled_date;
            $data["create_date"] = $charge->create_date;
            $data["retry_number"] = $charge->retry_number;
            $data["payment_date"] = $charge->payment_date;
            $data["payment_type"] = $charge->payment_type;
            $data["terminal_nsu"] = $charge->terminal_nsu;
            $data["authorization_code"] = $charge->authorization_code;
            $data["acquirer_transaction_id"] = $charge->acquirer_transaction_id;
            $data["installment"] = $charge->installment;

            $insert = DB::table('getnet_charges')->insertOrIgnore($data); // funciona
            if ($insert === 0){
                $update = DB::table('getnet_charges')->where('charge_id', $data['charge_id']);
                $update->update($data);
            }
        }

    }


}
