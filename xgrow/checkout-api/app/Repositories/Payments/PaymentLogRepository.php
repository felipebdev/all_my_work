<?php

namespace App\Repositories\Payments;

use App\PaymentLog;
use Illuminate\Support\Collection;

class PaymentLogRepository
{
    /**
     * List manual payment tries from Subscriber on a given payment (most recent first)
     *
     * @param  int  $paymentId
     * @return \Illuminate\Support\Collection
     */
    public function getSubscriberTriesByPaymentId(int $paymentId): Collection
    {
        return $this->getTriesByPaymentIdForAgent(PaymentLog::AGENT_SUBSCRIBER, $paymentId);
    }

    /**
     * Total number of payment tries from a Subscriber in a given payment
     *
     * @param  int  $paymentId
     * @return int
     */
    public function countSubscriberTriesByPaymentId(int $paymentId): int
    {
        return $this->countTriesByPaymentIdForAgent(PaymentLog::AGENT_SUBSCRIBER, $paymentId);
    }

    /**
     * Create a new log entry on manual payment tries
     *
     * @param  string  $platformId
     * @param  int  $paymentId
     * @param  int  $subscriberId
     */
    public function createSubscriberLog(string $platformId, int $paymentId, int $subscriberId): void
    {
        $paymentLog = new PaymentLog();
        $paymentLog->platform_id = $platformId;
        $paymentLog->payment_id = $paymentId;
        $paymentLog->subscriber_id = $subscriberId;
        $paymentLog->agent = PaymentLog::AGENT_SUBSCRIBER;
        $paymentLog->save();
    }

    /**
     * List manual payment tries from Client on a given payment (most recent first)
     *
     * @param  int  $paymentId
     * @return \Illuminate\Support\Collection
     */
    public function getClientTriesByPaymentId(int $paymentId): Collection
    {
        return $this->getTriesByPaymentIdForAgent(PaymentLog::AGENT_CLIENT, $paymentId);
    }

    /**
     * Total number of payment tries from a Client in a given payment
     *
     * @param  int  $paymentId
     * @return int
     */
    public function countClientTriesByPaymentId(int $paymentId): int
    {
        return $this->countTriesByPaymentIdForAgent(PaymentLog::AGENT_CLIENT, $paymentId);
    }

    /**
     * Create a new log entry on manual payment tries
     *
     * @param  string  $platformId
     * @param  int  $paymentId
     * @param  int  $userId
     */
    public function createClientLog(string $platformId, int $paymentId, int $userId): void
    {
        $paymentLog = new PaymentLog();
        $paymentLog->platform_id = $platformId;
        $paymentLog->payment_id = $paymentId;
        $paymentLog->user_id = $userId;
        $paymentLog->agent = PaymentLog::AGENT_CLIENT;
        $paymentLog->save();
    }

    /**
     * List manual payment tries from an Agent type on a given payment (most recent first)
     *
     * @param  int  $paymentId
     * @return \Illuminate\Support\Collection
     */
    protected function getTriesByPaymentIdForAgent(string $agent, int $paymentId): Collection
    {
        return PaymentLog::where('payment_id', $paymentId)
            ->where('agent', $agent)
            ->orderBy('created_at', 'DESC')
            ->get();
    }

    /**
     * Total number of payment tries from an Agent type on a given payment
     *
     * @param  int  $paymentId
     * @return int
     */
    protected function countTriesByPaymentIdForAgent(string $agent, int $paymentId): int
    {
        return PaymentLog::where('payment_id', $paymentId)->where('agent', $agent)->count();
    }
}
