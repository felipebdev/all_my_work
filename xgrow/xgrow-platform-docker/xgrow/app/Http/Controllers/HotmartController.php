<?php

namespace App\Http\Controllers;

use App\Constants;
use App\IntegrationType;
use App\Services\EmailService;
use http\Env\Response;
use Illuminate\Http\Request;
use Auth;
use App\Subscriber;
use App\Subscription;
use App\Integration;
use App\Plan;
use Illuminate\Support\Facades\Log;

class HotmartController extends Controller
{
    public function principal($integration_id, Request $request)
    {

        Log::info('Inicio Hotmart => ' . date('d/m/Y H:i:s'));
        Log::info($request->all());

        $integration = Integration::where('id', $integration_id)->first();
        $platform_id = $integration->platform_id;

        Log::info("Se houver o parametro subscriberCode deverá cancelar a assinatura");
        if (isset($request->subscriberCode)) {
            Log::info("Possui o parametro subscriberCode: {$request->subscriberCode}, irá efetuar o cancelamento da assinatura e do assinante...");
            return $this->unsubscriber($request, $platform_id);
        }
        Log::info("Não possui o parametro subscriberCode, segue com as demais ações...");

        if($integration === null) {
            Log::info("Integracao nao encontrada: $integration_id");
            return response()->json(['error' => 'Integração inexistente.'], 404);
        }

        if ($request->hottok !== $integration->source_token) {
            Log::info("Falha na autenticação: request->hottok:[$request->hottok] >> integration->source_token:[$integration->source_token]");
            return response()->json(['status' => 'error', 'message' => 'Autenticação inválida!'], 511);
        }

        Log::info("platform_id: $platform_id");

        $plan = $this->verifyPlan($request, $platform_id);

        Log::info("Status: request->status: {$request->status}");

        $status = Integration::hotmartPurchaseStatus($request->status);

        $type = 'subscription';
        if (isset($request->name_subscription_plan) && $request->name_subscription_plan !== '') { // subscription
            Log::info("Entrou em mode subscription: [campo: request->name_subscription_plan]: [valor: {$request->name_subscription_plan}]");
            $status = Integration::hotmartSubscriptionStatus($request->subscription_status);
        }

        else { // sale
            $type = 'sale';
        }

        Log::info("Type: $type >> Status: $status");

        $subscriber = $this->createFindSubscriber($request, $plan, $integration, $platform_id, $status);

        if ($type === 'subscription') {
            $subscription = $this->createFindSubscription($request, $plan, $integration, $platform_id, $status, $subscriber);
            return response()->json(['status' => 'success', 'message' => 'Sucesso.']);
        }

        return response()->json(['status' => 'success', 'message' => 'Sucesso.']);

    }

    private function unsubscriber($request, $platform_id)
    {
        $subscriberIntegration = IntegrationType::whereIntegrationTypeId($request->subscriberCode)->first();
        if ($subscriberIntegration === null) {
            return response()->json(['status' => 'error', 'message' => 'Assinante não encontrado: [#'.$request->subscriberCode.']']);
        }

        $subscriber = Subscriber::find($subscriberIntegration->integratable_id);

        if ($subscriber === null) {
            return response()->json(['status' => 'error', 'message' => 'Assinante não encontrado: [#'.$subscriberIntegration->integratable_id.']']);
        }

        $subscription = Subscription::whereSubscriberId($subscriber->id)->wherePlatformId($platform_id)->first();
        if ($subscription === null) {
            return response()->json(['status' => 'error', 'message' => 'Assinatura não encontrada para esse assinante: [assinante#'.$subscriber->id.']']);
        }

        Log::info("Alterar status assinatura");
        Log::info("Status atual[canceled_at]: {$subscription->canceled_at} será cancelada agora [subscriptionId: {$subscription->id}]");
        $this->alterStatusSubscription($subscription->id, $platform_id);

        Log::info("Alterar status assinante");
        Log::info("Status atual: {$subscriber->status} >> Status será: ".Subscriber::STATUS_CANCELED);
        $this->alterStatusSubscriber($subscriber->id, Subscriber::STATUS_CANCELED);

        return response()->json(['status' => 'success']);
    }

    private function alterStatusSubscription($subscriptionId)
    {
        $subscription = Subscription::find($subscriptionId);
        if($subscription === null) {
            return response()->json(['status' => 'error', 'message' => 'Assinatura não encontrada para cancelar']);
        }

        Log::info("Vamos cancelar a assinatura: [subscription->id: {$subscription->id}]");
        Log::info("Data de cancelamento antes: " . $subscription->canceled_at);
        Log::info("Data de cancelamento date(): " . date('Y-m-d H:i:s'));
        $subscription->update([
            'status' => Subscription::STATUS_CANCELED,
            'status_updated_at' => now(),
            'canceled_at' => date('Y-m-d H:i:s')
        ]);
        Log::info("Data de cancelamento depois: " . $subscription->canceled_at);
    }

    private function alterStatusSubscriber($subscriberId, $status)
    {
        Subscriber::whereId($subscriberId)->update(['status' => $status]);
    }

    private function createFindSubscriber($request, $plan, $integration, $platform_id, $status)
    {
        Log::info("=====> createFindSubscriber");
        $integrationSubscriber = IntegrationType::whereIntegrationTypeId($request->subscriber_code)
            ->whereIntegratableType(Subscriber::class)
            ->first();

        if ($integrationSubscriber !== null) {
            $subscriber = Subscriber::find($integrationSubscriber->integratable_id);
            if ($subscriber === null) {
                Log::info("Assinante não encontrado: [#$integrationSubscriber->integratable_id]");
                return response()->json(['error' => 'Assinante não encontrado: [#'.$integrationSubscriber->integratable_id.']'], 404);
            }

            if ($status !== $subscriber->status) {
                Log::info("Alterar status");
                Log::info("Status atual: {$subscriber->status} >> Status vindo da integração: $status");
                $this->alterStatusSubscriber($subscriber->id, $status);
            }

            return $subscriber;
        }

        $subscriber = new Subscriber;

        $subscriber->platform_id = $platform_id;
        $subscriber->plan_id = $plan->id;
        $subscriber->email = $request->email;
        $subscriber->name = ($request->name);
        $subscriber->raw_password = keygen(12);
        $subscriber->status = $status;
        $subscriber->main_phone = "{$request->phone_checkout_local_code}-{$request->phone_checkout_number}";
        $subscriber->cel_phone = "{$request->phone_local_code}-{$request->phone_number}";
        $subscriber->tax_id_number = $request->doc;
        $subscriber->address_street = ($request->address);
        $subscriber->address_number = $request->address_number;

        $subscriber->address_comp = ($request->address_comp);
        $subscriber->address_district = ($request->address_district);
        $subscriber->address_city = ($request->address_city);

        $subscriber->address_state = ($request->address_state);

        $subscriber->address_country = ($request->address_country);
        $subscriber->address_zipcode = $request->address_zip_code;
        $subscriber->source_register = Subscriber::SOURCE_INTEGRATION;

        $subscriber->save();

        Log::info("Criou assinante: {$subscriber->id}");
        Log::info("request->subscriber_code: {$request->subscriber_code}");

        $subscriber->integratable()->delete();

        if($request->subscriber_code === null) {
            $subscriber->delete();
            Log::info("Deletou assinante pq não recebeu o subscriber_code do hotmart");

            return response()->json(['status' => 'error', 'message' => 'Código da integração do assinante não pode ser nulo.'], 201);
        }
        $subscriber->integratable()->create(['integration_id' => $integration->id, 'integration_type_id' => $request->subscriber_code]);
        Log::info("Integrou subscriber->id {$subscriber->id} com request->subscriber_code: {$request->subscriber_code}");

        $emailService = new EmailService();
        $emailService->sendMailNewRegisterSubscriber($subscriber);
        Log::info("Envio de e-mail com os dados do assinante [se estiver habilitado no plano]");

        Log::info("Retornando assinante");
        return $subscriber;
    }

    private function createFindSubscription($request, $plan, $integration, $platform_id, $status, $subscriber)
    {
        Log::info("=====> createFindSubscription");
        $subscription = Subscription::whereSubscriberId($subscriber->id)->where('platform_id', $platform_id)->first();

        if ($subscription !== null) {

//            if (isset($request->subscriberCode)) {
//                Log::info("Alterar status assinatura");
//                Log::info("Status atual[canceled_at]: {$subscription->canceled_at} >> Status vindo da integração: $status");
//                $this->alterStatusSubscription($subscriber->id);
//
//                Log::info("Alterar status assinante");
//                Log::info("Status atual: {$subscriber->status} >> Status vindo da integração: $status");
//                $this->alterStatusSubscriber($subscriber->id, Subscriber::STATUS_CANCELED);
//            }

            return $subscription;
        }

        $subscription = new Subscription;

        $subscription->platform_id = $platform_id;
        $subscription->plan_id = $plan->id;
        $subscription->subscriber_id = $subscriber->id;
        $subscription->gateway_transaction_id = $request->transaction_ext;

        $subscription->save();

        $subscription->integratable()->delete();
        $subscription->integratable()->create(['integration_id' => $integration->id, 'integration_type_id' => $request->transaction_ext]);
        Log::info("Integrou subscription->id {$subscription->id} com request->transaction_ext: {$request->transaction_ext}");

        Log::info("Alterando o status do assinante de {$subscriber->status} para $status");
        $subscriber->status = $status;
        $subscriber->save();
        Log::info("Status alterado para {$subscriber->status}");

        Log::info("Retornando assinatura");
        return $subscription;
    }

    private function verifyPlan($request, $platform_id)
    {
        $plan = Plan::where([
            ['name', $request->name_subscription_plan],
            ['platform_id', $platform_id]
        ])->first();

        Log::info("Verifica se existe o plano");
        if (empty($plan) && isset($request->name_subscription_plan)) {
            Log::info("Não existe o plano, então vai criar");
            $plan = new Plan;

            $plan->platform_id = $platform_id;
            $plan->name = trim($request->callback_type);
            $plan->recurrence = $request->recurrency_period;
            $plan->status = 1;
            $plan->currency = $request->currency_code_from;
            $plan->price = $request->price;
            $plan->setup_price = 0;
            $plan->freedays = 0;
            $plan->freedays_type = 'free';
            $plan->charge_until = $request->maxChargeCycles;

            $plan->save();

            $plan->integratable()->delete();
            $integration = Integration::where('id_integration', '=', Constants::CONSTANT_INTEGRATION_HOTMART)->where('platform_id', $platform_id)->first();
            $plan->integratable()->create(['integration_id' => $integration->id, 'integration_type_id' => $request->transaction]);
            Log::info("Após criar o plano integra com nosso sistema");
            Log::info("plan->id: {$plan->id} intgrado com request->transaction: {$request->transaction}");
        }

        Log::info("Retornando plano");
        return $plan;
    }


    /// Fontes antigos, remover depois
    /// //    public function purchases($id, Request $request)
    ////    {
    ////
    ////        $status = Integration::hotmartPurchaseStatus($request->status);
    ////
    ////        $subscriberExists = Subscriber::where([
    ////            ['email', $request->email],
    ////            ['subscribers.platform_id', $id]
    ////        ])->first();
    ////
    ////        if ($status == 'canceled') {
    ////
    ////            if (empty($subscriberExists)) {
    ////                return response()->json(['error' => 'E-mail não cadastrado na base!']);
    ////            }
    ////
    ////            $subscriberExists->status = 'canceled';
    ////            $subscriberExists->save();
    ////
    ////            $subscriptionExists = Subscription::where('subscriber_id', $subscriberExists->id)
    ////                ->whereNull('canceled_at')
    ////                ->first();
    ////
    ////            $subscriptionExists->canceled_at = \Carbon\Carbon::now();
    ////            $subscriptionExists->save();
    ////
    ////            return response()->json(['success' => 'Usuário cancelado com sucesso!']);
    ////        }
    ////
    ////        if ($status == 'active' || $status == 'trial') {
    ////            /** CRIAÇÃO DE PLANO **/
    ////
    ////            /*
    ////            $subscriber->recurrency_period = $request->recurrency_period;
    ////            $subscriber->recurrency = $request->recurrency;
    ////            */
    ////            $plan = Plan::where([
    ////                ['name', $request->name_subscription_plan],
    ////                ['platform_id', $id]
    ////            ])->first();
    ////
    ////            if (empty($plan)) {
    ////                $plan = new Plan;
    ////
    ////                $plan->platform_id = $id;
    ////                $plan->name = trim($request->callback_type);
    ////                $plan->recurrence = $request->recurrency_period;
    ////                $plan->status = 1;
    ////                $plan->currency = $request->currency_code_from;
    ////                $plan->price = $request->price;
    ////                $plan->setup_price = 0;
    ////                $plan->freedays = 0;
    ////                $plan->freedays_type = 'free';
    ////                $plan->charge_until = $request->maxChargeCycles;
    ////
    ////                $plan->save();
    ////            }
    ////            /***********************************/
    ////            if (empty($subscriberExists)) {
    ////
    ////                $subscriber = new Subscriber;
    ////
    ////                $subscriber->platform_id = $id;
    ////                $subscriber->plan_id = $plan->id;
    ////                $subscriber->email = $request->email;
    ////                $subscriber->name = ($request->name);
    ////                $subscriber->raw_password =         $password_data = "0123456789abcdefghijklmnopqrstuvyxwz";;
    ////                $subscriber->status = $status;
    ////                $subscriber->main_phone = "{$request->phone_checkout_local_code}-{$request->phone_checkout_number}";
    ////                $subscriber->cel_phone = "{$request->phone_local_code}-{$request->phone_number}";
    ////                $subscriber->tax_id_number = $request->doc;
    ////                $subscriber->address_street = ($request->address);
    ////                $subscriber->address_number = $request->address_number;
    ////
    ////                $subscriber->address_comp = ($request->address_comp);
    ////                $subscriber->address_district = ($request->address_district);
    ////                $subscriber->address_city = ($request->address_city);
    ////
    ////                $subscriber->address_state = ($request->address_state);
    ////
    ////                $subscriber->address_country = ($request->address_country);
    ////                $subscriber->address_zipcode = $request->address_zip_code;
    ////
    ////                $subscriber->save();
    ////            }
    ////
    ////            $subscriptionExists = Subscription::where('subscriber_id', $subscriber->id ?? $subscriberExists->id)
    ////                ->whereNull('canceled_at')
    ////                ->first();
    ////
    ////            if (!empty($subscriptionExists)) {
    ////                return response()->json(['error' => 'Esse e-mail já possui uma assinatura ativa!']);
    ////            }
    ////
    ////            $subscription = new Subscription;
    ////
    ////            $subscription->platform_id = $id;
    ////            $subscription->plan_id = $plan->id;
    ////            $subscription->subscriber_id = $subscriber->id ?? $subscriberExists->id;
    ////            $subscription->gateway_transaction_id = $request->transaction;
    ////
    ////            $subscription->save();
    ////
    ////            //Mudança de status do assinante
    ////            $subscriberExists->status = $status;
    ////            $subscriberExists->save();
    ////
    ////            return response()->json(['success' => 'Assinatura ativada com sucesso!']);
    ////        }
    ////    }
    ////
    ////    public function unsubscribe($id, Request $request)
    ////    {
    ////        $subscriber = Subscriber::where([
    ////            ['email', $request->userEmail],
    ////            ['subscribers.platform_id', $id]
    ////        ])->first();
    ////
    ////        $subscriber->status = 'canceled';
    ////
    ////        $subscriber->save();
    ////
    ////        $subscription = Subscription::where('subscriber_id', $subscriber->id)
    ////            ->whereNull('canceled_at')
    ////            ->first();
    ////
    ////        $subscription->canceled_at = \Carbon\Carbon::now();
    ////        $subscription->save();
    ////
    ////        return response()->json($subscriber);
    ////    }
    ////
    ////    public function imports($platform_id, $arquivo)
    ////    {
    ////        ini_set('max_execution_time', 0);
    ////        $data = [];
    ////        $csv = file("subscribers_{$arquivo}.csv", FILE_IGNORE_NEW_LINES);
    ////
    ////        foreach ($csv as $key => $value) {
    ////            $line = str_getcsv($value, ';');
    ////
    ////            //$data[$key]['status'] = $line[0];
    ////            //$data[$key]['name'] = $line[1];
    ////            //$data[$key]['gateway_transaction_id'] = $line[2];
    ////            //$data[$key]['plan_id'] = $line[3];
    ////
    ////            $dateParcial1 = explode(' ', $line[4]);
    ////            $date1 = explode('/', $dateParcial1[0]);
    ////            $line[4] = "{$date1[2]}-{$date1[1]}-{$date1[0]} 07:00:00";
    ////
    ////            //$data[$key]['canceled_at'] = $line[5];
    ////            if (!empty($line[5])) {
    ////                $dateParcial2 = explode(' ', $line[5]);
    ////                $date2 = explode('/', $dateParcial2[0]);
    ////                $line[5] = "{$date2[2]}-{$date2[1]}-{$date2[0]} 07:00:00";
    ////            }
    ////
    ////            //$data[$key]['email'] = $line[6];
    ////            //$data[$key]['main_phone'] = "{$line[7]}-{$line[8]}";
    ////            //$data[$key]['address_zipcode'] = $line[9];
    ////            //$data[$key]['address_city'] = $line[10];
    ////            //$data[$key]['address_state'] = $line[11];
    ////            //$data[$key]['address_district'] = $line[12];
    ////            //$data[$key]['address_country'] = $line[13];
    ////            //$data[$key]['address_street'] = $line[14];
    ////            //$data[$key]['address_number'] = $line[15];
    ////            //$data[$key]['address_comp'] = $line[16];
    ////            $exists = Subscriber::where([
    ////                ['email', $line[6]],
    ////                ['platform_id', $platform_id],
    ////            ])->first();
    ////
    ////            if (empty($exists)) {
    ////
    ////                $subscriber = new Subscriber;
    ////
    ////                $subscriber->platform_id = $platform_id;
    ////                $subscriber->plan_id = $line[3];
    ////                $subscriber->email = $line[6];
    ////                $subscriber->name = $line[1];
    ////
    ////                $password_data = "0123456789abcdefghijklmnopqrstuvyxwz";
    ////                $subscriber->password = Hash::make(str_shuffle($password_data));
    ////
    ////                $subscriber->status = $line[0];
    ////                $subscriber->main_phone = "{$line[7]}-{$line[8]}";
    ////                $subscriber->address_street = $line[14];
    ////                $subscriber->address_number = $line[15];
    ////                $subscriber->address_comp = $line[16];
    ////                $subscriber->address_district = $line[12];
    ////                $subscriber->address_city = $line[10];
    ////                $subscriber->address_state = $line[11];
    ////                $subscriber->address_country = $line[13];
    ////                $subscriber->address_zipcode = $line[9];
    ////                $subscriber->created_at = \Carbon\Carbon::parse($line[4])->format('Y-m-d H:i:s');
    ////
    ////                $subscriber->save();
    ////
    ////                $subscription = new Subscription;
    ////                $subscription->platform_id = $platform_id;
    ////                $subscription->plan_id = $line[3];
    ////                $subscription->subscriber_id = $subscriber->id;
    ////                $subscription->gateway_transaction_id = $line[2];
    ////                $subscription->created_at = \Carbon\Carbon::parse($line[4])->format('Y-m-d H:i:s');
    ////                $subscription->canceled_at = !empty($line[5]) ? \Carbon\Carbon::parse($line[5])->format('Y-m-d H:i:s') : null;
    ////
    ////                $subscription->save();
    ////            } else {
    ////                $subscription = new Subscription;
    ////                $subscription->platform_id = $platform_id;
    ////                $subscription->plan_id = $line[3];
    ////                $subscription->subscriber_id = $exists->id;
    ////                $subscription->gateway_transaction_id = $line[2];
    ////                $subscription->created_at = \Carbon\Carbon::parse($line[4])->format('Y-m-d H:i:s');
    ////                $subscription->canceled_at = !empty($line[5]) ? \Carbon\Carbon::parse($line[5])->format('Y-m-d H:i:s') : null;
    ////
    ////                $subscription->save();
    ////            }
    ////        }
    ////        echo 'Funcionou';
    ////        exit;
    ////        echo '<pre>';
    ////        print_r($data);
    ////        echo '</pre>';
    ////    }
}
