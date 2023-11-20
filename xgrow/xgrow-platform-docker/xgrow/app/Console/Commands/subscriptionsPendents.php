<?php

namespace App\Console\Commands;

use DB;
use App\GetnetCharge;
use App\Subscription;
use Illuminate\Console\Command;

class subscriptionsPendents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:pendents';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Suspende assinaturas até o pagamento, com limite de dias estipulado';

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
        $charges = GetnetCharge::where('scheduled_date', '<=', DB::raw("scheduled_date <= substring(NOW(), 1, 10)"))
            ->where('status', '<>', 'paid')
            ->where('status', '<>', 'scheduled')
            ->get();

        if ($charges->count() > 0) {
            foreach($charges as $charge) {
                $subscription = Subscription::join('integration_types', 'subscriptions.id', '=', 'integration_types.integratable_id')
                    ->join('integrations', 'integrations.id', '=', 'integration_types.integration_id')
                    ->where('integration_types.integration_type_id', '=', $charge->subscription_id)
                    ->select('subscriptions.id')
                    ->first();

                if ($subscription !== null) {
                    $sub = Subscription::where('id', '=', $subscription->id);
                    $sub->update(['payment_pendent' => date_format(now(), 'Y-m-d')]);
                }
            }
        }
    }
}
