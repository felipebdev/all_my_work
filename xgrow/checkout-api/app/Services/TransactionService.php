<?php

namespace App\Services;

use App\Enums\TransactionResponseEnum;
use App\Transaction;
use App\Utils\ArrayList;
use App\Utils\TransactionItem;
use App\Utils\TriggerIntegrationJob;
use Exception;
use Illuminate\Support\Facades\DB;

class TransactionService
{
    use TriggerIntegrationJob;

    const NOT_SPECIFIED_CODE = '9999';

    const DEFAULT_TRANSACTION_MESSAGE = TransactionResponseEnum::CODE_1000;

    public function create(
        string $platformId,
        int $subscriberId,
        string $orderCode,
        ?string $transactionId = null,
        ?string $transactionCode = null,
        float $total = 0.0,
        array $plans = [],
        string $type = 'credit_card',
        ?string $origin = null,
        ?string $cardId = null,
        ?int $paymentId = null,
        string $status = 'failed'
    ) {
        try {
            DB::beginTransaction();

            $list = new ArrayList(TransactionItem::class);
            foreach ($plans as $plan) {
                $list[] = $plan;
            }

            $type = $type === 'boleto' ? Transaction::TYPE_BANK_SLIP : $type;

            $transactionMessage = TransactionResponseEnum::isValidName("CODE_{$transactionCode}")
                ? TransactionResponseEnum::getValue("CODE_{$transactionCode}")
                : self::DEFAULT_TRANSACTION_MESSAGE;

            $transaction = Transaction::create([
                'platform_id' => $platformId,
                'subscriber_id' => $subscriberId,
                'order_code' => $orderCode,
                'transaction_id' => $transactionId,
                'transaction_code' => $transactionCode,
                'transaction_message' => $transactionMessage,
                'total' => $total,
                'status' => $status,
                'type' => $type,
                'origin' => $origin ?? Transaction::ORIGIN_TRANSACTION,
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
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

        if ($status === 'failed') {
            $this->triggerTransactionRefusedEvent($transaction);
        }
    }
}
