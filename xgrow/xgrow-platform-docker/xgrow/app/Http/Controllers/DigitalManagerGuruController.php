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

class DigitalManagerGuruController extends Controller
{
     public function principal($integration_id, Request $request)
    {
        $integration = Integration::where('id', $integration_id)->first();

        if($integration === null) {
            return response()->json(['error' => 'Integração inexistente.'], 404);
        }

        $platform_id = $integration->platform_id;

        Log::info('Inicio Digital Manager Guru => ' . date('d/m/Y H:i:s'));
        Log::info($request->all());

        $plan = $this->verifyPlan($request, $platform_id);

        $source_token = json_decode($integration->source_token, true);

        // if ($request->api_token !== $source_token['api_key']) {
        //     return response()->json(['status' => 'error', 'message' => 'Autenticação inválida!'], 511);
        // }

        switch($request->status) {
            // case 15:
            //     return $this->alterStatusSubscriber($request->trans_cod, Subscriber::gurumanager);
            //     break;
            case 'approved':
                return $this->purchases($platform_id, $request, $plan);
                break;
            case 'canceled':
            case 'blocked':
            case 'rejected':
                return $this->unsubscribe($platform_id, $request);
                break;

        }

    }

    public function temp()
    {
        return response()->json(['status' => 'error'], 500);
    }

    public function purchases($platform_id, $request, $plan)
    {
        $status = $request->status;

        $subscriber = Subscriber::where([
            ['email', $request->contact['email']],
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

        if ($status == 'approved' || $status == 'trial') {

            if (empty($subscriber)) {
                $subscriber = $this->createSubscriber($request, $platform_id, $plan);
            }

            if ($plan->type_plan === 'P') {
                $subscription = Subscription::where('subscriber_id', $subscriber->id)
                ->where('plan_id', $plan->id)
                ->whereNull('canceled_at')
                ->first();

                if (empty($subscription)) {
                    $this->createSubscription($request, $platform_id, $plan, $subscriber);
                }
                if ($request->id === null) {
                    $subscription->integratable()->delete();
                    $subscription->delete();
                    $subscriber->delete();
                    return response()->json(['status' => 'error', 'message' => 'Código da integração da assinatura não pode ser nulo.'], 201);
                }

                $subscriber->status = Subscriber::STATUS_ACTIVE;
                $subscriber->save();
            }

            $emailService = new EmailService();
            $emailService->sendMailNewRegisterSubscriber($subscriber);

            return response()->json(['success' => 'Sucesso.']);
        }
    }

    public function unsubscribe($platform_id, Request $request)
    {

        $status = $request->status;

        if ($status !== Integration::gurumanager) {
            return response()->json(['status' => 'error', 'message' => 'Não aplicável'], 204);
        }

        $integrationType = IntegrationType::whereIntegrationTypeId($request->id)
            ->whereIntegratableType(Subscription::class)
            ->first();

        if ($integrationType === null) {
            return response()->json(['status' => 'error', 'message' => 'Contrato não encontrado [#'.$request->id.']'], 204);
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
            'status' => Integration::eduzzInvoiceStatusFinal($request->trans_status),
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
            ['name', $request->product['name']],
            ['platform_id', $platform_id]
        ])->first();

        if (empty($plan)) {
            $plan = new Plan;

            $plan->platform_id = $platform_id;
            $plan->name = trim($request->product['name']);
            $plan->recurrence = Integration::eduzzRecurrency('month');
            $plan->status = 1;
            $plan->currency = $request->payment['currency'];
            $plan->price = $request->product['total_value'];
            $plan->setup_price = 0;
            $plan->freedays = 0;
            $plan->freedays_type = 'free';
            $plan->charge_until = 0;
            $plan->type_plan = 'P';
            $plan->trigger_email = 1;

            $plan->save();

            $plan->integratable()->delete();
            $integration = Integration::where('id_integration', '=', Constants::CONSTANT_INTEGRATION_DIGITALMANAGERGURU)->where('platform_id', $platform_id)->first();
            $plan->integratable()->create(['integration_id' => $integration->id]);

        }

        return $plan;
    }

    private function createSubscriber($request, $platform_id, $plan)
    {
        if ($request->contact['email'] === null) {
            return response()->json(['status' => 'error', 'message' => 'E-mail não pode ser nulo!'], 201);
        }

        $subscriber = new Subscriber;

        $subscriber->platform_id = $platform_id;
        $subscriber->plan_id = $plan->id;
        $subscriber->email = $request->contact['email'];
        $subscriber->name = $request->contact['name'];
        $subscriber->document_number = $request->contact['doc'];
        $subscriber->raw_password = keygen(12);
        $subscriber->status = Subscriber::STATUS_ACTIVE;
        $subscriber->main_phone = $request->contact['phone_number'];
        $subscriber->cel_phone = $request->contact['phone_number'];
        $subscriber->tax_id_number = $request->contact['doc'];
        $subscriber->address_street = $request->contact['address'];
        $subscriber->address_number = $request->contact['address_number'];
        $subscriber->address_comp = $request->contact['address_comp'];
        $subscriber->address_district = $request->contact['address_district'];
        $subscriber->address_city = $request->contact['address_city'];
        $subscriber->address_state = $request->contact['address_state'];
        $subscriber->address_country = $request->contact['address_country'];
        $subscriber->address_zipcode = $request->contact['address_zip_code'];
        $subscriber->source_register = Subscriber::SOURCE_INTEGRATION;

        $subscriber->save();

        $subscriber->integratable()->delete();
        $integration = Integration::where('platform_id', $subscriber->platform_id)->where('id_integration', '=', Constants::CONSTANT_INTEGRATION_DIGITALMANAGERGURU)->first();
        if ($integration === null) {
            $subscriber->delete();
            return response()->json(['status' => 'error', 'message' => 'Integração não encontrada'], 201);
        }

        if($request->id === null) {
            $subscriber->delete();
            return response()->json(['status' => 'error', 'message' => 'Código da integração do assinante não pode ser nulo.'], 201);
        }
        $subscriber->integratable()->create(['integration_id' => $integration->id]);

        return $subscriber;
    }

    private function createSubscription($request, $platform_id, $plan, $subscriber)
    {
        $subscription = new Subscription;

        $subscription->platform_id = $platform_id;
        $subscription->plan_id = $plan->id;
        $subscription->subscriber_id = $subscriber->id;
        $subscription->gateway_transaction_id = $request->id;
        $subscription->payment_pendent = null;
        $subscription->order_number = null;
        $subscription->save();

        $subscription->integratable()->delete();
        $integration = Integration::where('platform_id', $subscriber->platform_id)->where('id_integration', '=', Constants::CONSTANT_INTEGRATION_DIGITALMANAGERGURU)->first();
        $subscription->integratable()->create(['integration_id' => $integration->id]);

        return $subscription;
    }


}
