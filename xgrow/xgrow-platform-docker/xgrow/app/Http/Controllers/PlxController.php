<?php

namespace App\Http\Controllers;

use App\Services\EmailService;
use App\Invoice;
use Illuminate\Support\Facades\Log;
use mysql_xdevapi\Result;
use StdClass;
use App\Constants;
use App\IntegrationType;
use App\Order;
use Illuminate\Http\Request;
use Auth;
use App\Subscriber;
use App\Subscription;
use App\Integration;
use App\Plan;

class PLXController extends Controller
{

    public function principal($integration_id, Request $request)
    {
        $integration = Integration::where('id', $integration_id)->first();

        if($integration === null) {
            return response()->json(['error' => 'Integração inexistente.'], 404);
        }

        $platform_id = $integration->platform_id;

        Log::info('Inicio PLX => ' . date('d/m/Y H:i:s'));
        Log::info($request->all());

        $plan = $this->verifyPlan($request, $platform_id);

        if ($request->api_key !== $integration->source_token) {
            return response()->json(['status' => 'error', 'message' => 'Autenticação inválida!'], 511);
        }

        if ($request->type === 'contract'){
            switch($request->recurrence_status) {
                case 1:
                case 2:
                case 9:
                case 10:
                    return $this->purchases($platform_id, $request, $plan);
                    break;
                case 3:
                case 4:
                case 7:
                    return $this->unsubscribe($platform_id, $request);
                    break;
            }
        }

        if ($request->type === 'invoice'){
            switch($request->trans_status) {
//                    case 1:
                case 6:
                case 7:
                case 9:
                case 10:
                case 11:
                case 15:
                    return $this->alterStatusSubscriber($request->trans_cod, Subscriber::STATUS_CANCELED);
                    break;
                case 3:
                    return $this->purchases($platform_id, $request, $plan);
                    break;
                case 4:
                    return $this->unsubscribe($platform_id, $request);
                    break;

            }
        }
    }

    public function temp()
    {
        return response()->json(['status' => 'error'], 500);
    }

    public function purchases($platform_id, $request, $plan)
    {
        $status = ($request->type === 'contract') ? Integration::PLXPurchaseStatus($request->recurrence_status) : Integration::PLXInvoiceStatus($request->trans_status);

        $subscriber = Subscriber::where([
            ['email', $request->cus_email],
            ['subscribers.platform_id', $platform_id]
        ])->first();

        if ($status == 'canceled') {

            if (empty($subscriber)) {
                return response()->json(['error' => 'E-mail não cadastrado na base!']);
            }

            $subscriber->status = 'canceled';
            $subscriber->save();

            $subscriptionExists = Subscription::where('subscriber_id', $subscriber->id)
                ->whereNull('canceled_at')
                ->first();

            $subscriptionExists->status  = Subscription::STATUS_CANCELED;
            $subscriptionExists->status_updated_at = \Carbon\Carbon::now();
            $subscriptionExists->canceled_at = \Carbon\Carbon::now();
            $subscriptionExists->save();

            return response()->json(['success' => 'Usuário cancelado com sucesso!']);
        }

        if ($status == 'active' || $status == 'trial') {

            if (empty($subscriber)) {
                $subscriber = $this->createSubscriber($request, $platform_id, $status, $plan);
            }

            if ($plan->type_plan === 'R' && $request->type === 'contract') { // if subscription
                $subscription = Subscription::where('subscriber_id', $subscriber->id)
                    ->whereNull('canceled_at')
                    ->first();

                if (empty($subscription)) {
                    $this->createSubscription($request, $platform_id, $plan, $subscriber);
                }

                /*
                if ($request->recurrence_cod === null) {
                    $subscription->integratable()->delete();
                    $subscription->delete();
                    $subscriber->delete();
                    return response()->json(['status' => 'error', 'message' => 'Código da integração da assinatura não pode ser nulo.'], 201);
                }
                */

                $subscriber->status = $status;
                $subscriber->save();
            }

            if(config('app.env') == 'production'){
                $emailService = new EmailService();
                $emailService->sendMailNewRegisterSubscriber($subscriber);
            }

            return response()->json(['success' => 'Sucesso.']);

//            Descomentar esse trecho após o entendimento de quando uma fatura/assinatura é paga (data de pagamento está vindo null nos testes recebidos da PLX [campo trans_paiddate])
//            $orderIntegration = ($plan->type_plan === 'R' && $request->type === 'contract') ? $request->trans_cod : $request->trans_cod;
//            $orderableId = ($plan->type_plan === 'R' && $request->type === 'contract') ? $subscription->id : $subscriber->id;
//
//
//            $dataOrder = [
//                'order_integration' => $orderIntegration,
//                'orderable_id' => $orderableId
//            ];
//
//            switch ($request->type) {
//                case 'contract':
//                    $subscription = Subscription::find($subscription->id);
//                    $orderId = $subscription->orderable()->create($dataOrder)->id;
//                    break;
//                default:
//                    $subscriber = Subscriber::find($subscriber->id);
//                    $orderId = $subscriber->orderable()->create($dataOrder)->id;
//                    break;
//            }
//
//            if ($orderId < 0) {
//                return response()->json(['status' => 'error', 'message' => 'Erro ao tentar criar o pedido'], 201);
//            }
//
//            return $this->payment($platform_id, $orderId, $request);

        }
    }

    public function unsubscribe($platform_id, Request $request)
    {

        $status = Integration::PLXPurchaseStatus($request->recurrence_status);

        if ($status !== Integration::STATUS_CANCELED) {
            return response()->json(['status' => 'error', 'message' => 'Não aplicável'], 204);
        }

        $integrationType = IntegrationType::whereIntegrationTypeId($request->trans_cod)
            ->whereIntegratableType(Subscription::class)
            ->first();

        if ($integrationType === null) {
            return response()->json(['status' => 'error', 'message' => 'Contrato não encontrado [#'.$request->trans_cod.']'], 204);
        }

        $subscription = Subscription::find($integrationType->integratable_id);
        if ($subscription === null) {
            return response()->json(['status' => 'error', 'message' => 'Assinatura não encontrada [#'.$integrationType->integratable_id.']'], 204);
        }

        $subscriber = Subscriber::find($subscription->subscriber_id);
        if ($subscriber === null) {
            return response()->json(['status' => 'error', 'message' => 'Assinante não encontrado [#'.$subscription->subscriber_id.']'], 204);
        }

        $subscription->status  = Subscription::STATUS_CANCELED;
        $subscription->status_updated_at = \Carbon\Carbon::now();
        $subscription->canceled_at = \Carbon\Carbon::now();
        $subscription->save();

        $subscriber->status = Subscriber::STATUS_CANCELED;
        $subscriber->save();

        return response()->json(['status' => 'Success']);

    }

    public function alterStatusSubscriber($trans_cod, $status)
    {


        $integrationType = IntegrationType::whereIntegrationTypeId($trans_cod)
            ->whereIntegratableType(Subscriber::class)
            ->first();

        if ($integrationType === null) {
            return response()->json(['status' => 'error', 'message' => 'Fatura de assinante não encontrada: [#'.$trans_cod.']'], 204);
        }

        $subscriber = Subscriber::find($integrationType->integratable_id);


        if ($subscriber === null) {
            return response()->json(['status' => 'error', 'message' => 'Assinante não encontrado: [#'.$integrationType->integratable_id.']'], 204);
        }

        $subscriber->status = $status;
        $subscriber->save();

        return response()->json(['status' => 'Success']);

    }

    public function payment($platformId, $orderId, $request)
    {

        $invoice = Invoice::create([
            'code' => $request->trans_cod,
            'status' => Integration::PLXInvoiceStatusFinal($request->trans_status),
            'amount' => $request->trans_paid,
            'paid_date' => $request->trans_paiddate,
            'paid_time' => $request->trans_paidtime,
            'platform_id' => $platformId,
            'order_id' => $orderId
        ]);

        if (!$invoice->id) {
            return response()->json(['status' => 'error', 'message' => 'Erro na inserção da fatura.'], 500);
        }

        return response()->json(['status' => 'Success.']);
    }

    public function verifyPlan($request, $platform_id)
    {
        $plan = Plan::where([
            ['name', $request->recurrence_plan],
            ['platform_id', $platform_id]
        ])->first();

        if (empty($plan)) {
            $plan = new Plan;

            $plan->platform_id = $platform_id;
            $plan->name = trim($request->recurrence_plan);
            $plan->recurrence = Integration::PLXRecurrency($request->recurrence_interval_type);
            $plan->status = 1;
            $plan->currency = $request->trans_currency;
            $plan->price = $request->trans_value;
            $plan->setup_price = 0;
            $plan->freedays = 0;
            $plan->freedays_type = 'free';
            $plan->charge_until = $request->recurrence_interval;
            $plan->type_plan = 'R';
            $plan->trigger_email = 1;

            if ($request->product_chargetype === 'N') {
                $plan->type_plan = 'P';
            }

            $plan->save();

            $plan->integratable()->delete();
            $integration = Integration::where('id_integration', '=', Constants::CONSTANT_INTEGRATION_PLX)->where('platform_id', $platform_id)->first();
            $plan->integratable()->create(['integration_id' => $integration->id, 'integration_type_id' => $request->product_cod]);

        }

        return $plan;
    }

    private function createSubscriber($request, $platform_id, $status, $plan)
    {
        if ($request->cus_email === null) {
            return response()->json(['status' => 'error', 'message' => 'E-mail não pode ser nulo!'], 201);
        }

        $subscriber = new Subscriber;

        $subscriber->platform_id = $platform_id;
        $subscriber->plan_id = $plan->id;
        $subscriber->email = $request->cus_email;
        $subscriber->name = $request->cus_name;
        $subscriber->document_number = $request->cus_taxnumber;
        $subscriber->raw_password = keygen(12);
        $subscriber->status = $status;
        $subscriber->main_phone = $request->cus_tel;
        $subscriber->cel_phone = $request->cus_cel;
        $subscriber->tax_id_number = $request->cus_taxnumber;
        $subscriber->address_street = $request->cus_address;
        $subscriber->address_number = $request->cus_address_number;
        $subscriber->address_comp = $request->cus_address_comp;
        $subscriber->address_district = $request->cus_address_district;
        $subscriber->address_city = $request->cus_address_city;
        $subscriber->address_state = $request->cus_address_state;
        $subscriber->address_country = $request->cus_address_country;
        $subscriber->address_zipcode = $request->cus_address_zip_code;
        $subscriber->source_register = Subscriber::SOURCE_INTEGRATION;

        $subscriber->save();

        $integration = Integration::where('platform_id', $subscriber->platform_id)->where('id_integration', '=', Constants::CONSTANT_INTEGRATION_PLX)->first();
        if ($integration === null) {
            $subscriber->delete();
            return response()->json(['status' => 'error', 'message' => 'Integração não encontrada'], 201);
        }

        if($request->trans_cod === null) {
            $subscriber->delete();
            return response()->json(['status' => 'error', 'message' => 'Código da integração do assinante não pode ser nulo.'], 201);
        }
        $subscriber->integratable()->create(['integration_id' => $integration->id, 'integration_type_id' => $request->trans_cod]);

        return $subscriber;
    }

    private function createSubscription($request, $platform_id, $plan, $subscriber)
    {
        $subscription = new Subscription;

        $subscription->platform_id = $platform_id;
        $subscription->plan_id = $plan->id;
        $subscription->subscriber_id = $subscriber->id;
        $subscription->gateway_transaction_id = $request->trans_cod;

        $subscription->save();

        $subscription->integratable()->delete();
        $integration = Integration::where('platform_id', $subscriber->platform_id)->where('id_integration', '=', Constants::CONSTANT_INTEGRATION_PLX)->first();
        $subscription->integratable()->create(['integration_id' => $integration->id, 'integration_type_id' => $request->trans_cod]);

        //variavel recurrence_cod não fazia sentido
        //$subscription->integratable()->create(['integration_id' => $integration->id, 'integration_type_id' => $request->recurrence_cod]);

        return $subscription;
    }


}
