<?php

namespace App\Console\Commands;

use App\Plan;
use App\Platform;
use App\Producer;
use App\ProducerProduct;
use App\Services\Finances\Objects\PriceTag;
use App\Services\Finances\Split\Calculator\Objects\XgrowSplitResult;
use App\Services\Finances\Split\DetailedSplitService;
use App\Services\Mundipagg\Objects\OrderResult;
use App\Services\Mundipagg\ProducerSplitService;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use MundiAPILib\Models\GetOrderResponse;

class RecreateMissingPaymentPlanSplit extends Command
{
    protected $signature = 'xgrow:fix:sx151 {payment_ids*}';

    protected $description = 'Fix payments with ';

    public function handle()
    {
        $paymentIds = $this->argument('payment_ids');

        $ids = join(', ', $paymentIds);

        $sql = <<<SQL
    select
        payments.platform_id as platform_id,
        payments.id as payment_id,
        payments.order_code as order_code,
        payments.payment_date as payment_date,
        pp.id as payment_plan_id,
        pp.plan_id as payment_plan_plan_id,
        pp.payment_id as payment_plan_payment_id,
        pp.type as payment_plan_type,
        pp.tax_value as payment_plan_tax_value,
        pp.customer_value as payment_plan_customer_value,
        pp.plan_value as payment_plan_plan_value
    from payments
    inner join payment_plan pp on pp.payment_id = payments.id
    where payments.id IN ($ids)
SQL;

        $payments = DB::select($sql);

        foreach ($payments as $payment) {
            $platform = Platform::find($payment->platform_id);

            $producerSplitService = new class extends ProducerSplitService {
                public static $baseDate;

                /**
                 * Override method to set base date for tests
                 */
                protected function getProducerContractsFromPriceTags(Collection $priceTags): Collection
                {
                    $planIds = $priceTags->map(fn(PriceTag $priceTag) => $priceTag->getId());

                    $productIds = Plan::whereIn('id', $planIds)->get()->pluck('product_id')->toArray();

                    return ProducerProduct::whereIn('product_id', $productIds)
                        ->where('status', ProducerProduct::STATUS_ACTIVE)
                        ->whereRelation('producer', 'type', Producer::TYPE_PRODUCER)
                        ->where('created_at', '<=', self::$baseDate)
                        ->where(function ($query) {
                            $query->whereRaw('contract_limit >= ?', self::$baseDate);
                            $query->orWhereNull('contract_limit');
                        })
                        ->get();
                }
            };

            $producerSplitService::$baseDate = $payment->payment_date;

            $xgrowSplitResult = new XgrowSplitResult(
                $payment->payment_plan_customer_value,
                $payment->payment_plan_tax_value,
                $payment->payment_plan_tax_value,
                CLIENT_TRANSACTION_TAX
            );

            $splitResult = $producerSplitService->calculateSplit(
                $xgrowSplitResult,
                PriceTag::fromDecimal($payment->payment_plan_plan_id, $payment->payment_plan_plan_value)
            );

            $orderResult = OrderResult::fromMundipagg(new GetOrderResponse(), [$splitResult]);

            $details = DetailedSplitService::create(
                $payment->platform_id,
                $platform->client->id,
                [$payment->payment_id],
                $payment->order_code,
                $orderResult
            );

            $total = count($details) ?? 0;
            if ($total == 0) {
                Log::warning('No payment_plan_split created', [
                    'payment_ids' => $paymentIds ?? 'null',
                ]);
            }
        }

        return Command::SUCCESS;
    }
}
