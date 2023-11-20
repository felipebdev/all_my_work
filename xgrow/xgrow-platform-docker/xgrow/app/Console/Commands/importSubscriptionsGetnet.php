<?php

namespace App\Console\Commands;

use App\Constants;
use App\Integration;
use App\Payment;
use App\Plan;
use App\Platform;
use App\Safe;
use App\Subscriber;
use App\Subscription;
use Illuminate\Console\Command;

class importSubscriptionsGetnet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importa assinaturas do Getnet para nosso banco de dados';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
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
}
