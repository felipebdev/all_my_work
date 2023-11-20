<?php

namespace App\Console\Commands;

use App\Logs\ChargeLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdatePaymentPlanStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'xgrow:update-payment-plan-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update payment_plan.status field with values from payment.status';

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
     * @return int
     */
    public function handle()
    {
       ChargeLog::info('Update payment_plan.status command starting');

       $start = time();

       DB::update('UPDATE payment_plan pp INNER JOIN payments p ON pp.payment_id = p.id
                         SET pp.status = p.status
                         WHERE pp.status IS NULL');

       $time = time() - $start;

       $message = "Comando executado com sucesso em {$time} segundo(s)";

       $this->info($message);

       ChargeLog::info($message);

       ChargeLog::info('Update payment_plan.status command finished');

    }
}
