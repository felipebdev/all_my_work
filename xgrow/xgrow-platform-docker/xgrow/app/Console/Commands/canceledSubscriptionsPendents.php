<?php

namespace App\Console\Commands;

use App\Subscription;
use Illuminate\Console\Command;

class canceledSubscriptionsPendents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:canceled-pendents';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cancela as assinaturas que passaram o limite de dias sem pagamento';

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
        $subscriptionsToCanceled = Subscription::join('integration_types', 'subscriptions.id', '=', 'integration_types.integratable_id')
            ->join('integrations',  'integration_types.integration_id',  '=',  'integrations.id')
            ->where(DB::raw("DATE_ADD(subscriptions.payment_pendent, INTERVAL integrations.days_limit_payment_pendent DAY)"), '<=', DB::raw("substring(NOW(), 1, 10)"))
            ->whereNull("canceled_at")
            ->orWhereRaw("date(canceled_at) = 0000-00-00 ")
            ->select('subscriptions.id')
            ->get();

        foreach ($subscriptionsToCanceled as $sub) {
            $subscription = Subscription::find($sub->id);
            $subscription->update(['canceled_at' => now()]);
        }
    }
}
