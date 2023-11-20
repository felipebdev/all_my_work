<?php

namespace App\Console\Commands;

use App\PaymentPlanSplit;
use App\Plan;
use App\Producer;
use App\ProducerProduct;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class XgrowXp2965FixPaymentPlanSplit extends Command
{

    protected $signature = 'xgrow:xp2965';

    protected $description = 'Fixes missing payment_plan_split.producer_product_id for affiliates';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $payments = PaymentPlanSplit::where('type', 'A')->whereNull('producer_product_id')->get();

        foreach ($payments as $payment) {
            $contractId = $this->getContractId($payment->plan_id, $payment->created_at);

            if ($contractId) {
                Log::debug("Updating payment_plan_split {$payment->id} to use producer_product_id = {$contractId}");
                $payment->producer_product_id = $contractId;
                $payment->timestamps = false;
                $payment->save();
            }
        }

        return Command::SUCCESS;
    }

    public function getContractId(int $planId, $date): ?int
    {
        $plan = Plan::find($planId);
        if (!$plan) {
            return null;
        }

        $productId = $plan->product_id;

        $contract = ProducerProduct::where('status', '<>', 'pending')
            ->whereRelation('producer', 'type', Producer::TYPE_AFFILIATE)
            ->where('product_id', $productId)
            ->where('created_at', '<', $date)
            ->where(function ($query) use ($date) {
                $query->where('contract_limit', '>=', $date);
                $query->orWhereNull('contract_limit');
            })
            ->where(function ($query) use ($date) {
                $query->where('canceled_at', '>=', $date);
                $query->orWhereNull('canceled_at');
            })
            ->first();

        if (!$contract) {
            return null;
        }

        return $contract->id;
    }

}
