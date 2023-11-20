<?php

namespace App\Services;

use App\Enums\TransactionResponseEnum;
use App\Utils\ArrayList;
use App\Transaction;
use App\Utils\TransactionItem;
use App\Utils\TriggerIntegrationJob;
use Exception;
use Illuminate\Support\Facades\DB;

class TransactionService
{
    use TriggerIntegrationJob;

    const NOT_SPECIFIED_CODE = '9999';

    public function create(
        string $platformId,
        int $subscriberId,
        string $orderCode,
        ?string $transactionId = null,
        ?string $transactionCode = null,
        float $total = 0.0,
        array $plans = [],
        string $type = 'credit_card',
        ?string $cardId = null,
        ?int $paymentId = null,
        string $status = 'failed'
    ) {
        try {
            DB::beginTransaction();

            $list = new ArrayList(TransactionItem::class);
            foreach ($plans as $plan) $list[] = $plan;
            $type = ($type === 'boleto') ? 'bank_slip' : $type;

            $transaction = Transaction::create([
                'platform_id' => $platformId,
                'subscriber_id' => $subscriberId,
                'order_code' => $orderCode,
                'transaction_id' => $transactionId,
                'transaction_code' => $transactionCode,
                'transaction_message' => (
                    (empty($transactionCode) || !(TransactionResponseEnum::isValidName("CODE_{$transactionCode}")))
                    ? TransactionResponseEnum::CODE_9999
                    : TransactionResponseEnum::getValue("CODE_{$transactionCode}")
                ),
                'total' => $total,
                'status' => $status,
                'type' => $type,
                'card_id' => $cardId,
                'payment_id' => $paymentId
            ]);

            $transactionPlans = [];
            foreach ($list as $plan) {
                $transactionPlans[$plan->code] = [
                    'price' => $plan->amount,
                    'type' => $plan->category
                ];
            }

            $transaction->plans()->attach($transactionPlans);
            DB::commit();
        }
        catch(Exception $e) {
            DB::rollBack();
            throw $e;
        }

        if ($status === 'failed') $this->triggerTransactionRefusedEvent($transaction);
    }
}
