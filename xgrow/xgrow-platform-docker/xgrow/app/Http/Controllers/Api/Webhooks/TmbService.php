<?php

namespace App\Http\Controllers\Api\Webhooks;

use App\Http\Controllers\Api\Webhooks\Objects\TmbSubscriber;
use App\Plan;
use App\Services\Contracts\SubscriptionServiceInterface;
use App\Services\EmailService;
use App\Subscriber;
use App\Subscription;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class TmbService
{

    const TMB_STATUS_EFETIVADO = 'Efetivado';
    const TMB_STATUS_CANCELADO = 'Cancelado';

    public const CNPJ_LENGTH = 14;
    public const CPF_LENGTH = 11;

    private EmailService $emailService;
    private SubscriptionServiceInterface $subscriptionService;

    public function __construct(
        EmailService $emailService,
        SubscriptionServiceInterface $subscriptionService
    ) {
        $this->emailService = $emailService;
        $this->subscriptionService = $subscriptionService;
    }

    public function process(array $request)
    {
        $status = $request['status_pedido'];

        $email = $request['email'];
        $planId = $request['id_externo'];

        $plan = Plan::find($planId);

        if (!$plan) {
            Log::warning('TBM: plan not found', [
                'request' => $request,
            ]);

            return false;
        }

        $platform = $plan->product->platform;

        $subscriber = $this->getSubscriberByPlatformAndEmail($platform->id, $email);

        if ($status == self::TMB_STATUS_EFETIVADO) {
            if ($subscriber) {
                $subscriber->status = Subscriber::STATUS_ACTIVE;
                $subscriber->save();

                // Verify if this subscriber has this plan
                $hasPlan = $this->subscriberHasPlan($subscriber->id, $plan->id);
                if ($hasPlan) {
                    Log::warning('TMB: user has plan', [
                        'request' => $request,
                    ]);

                    return false;
                }

                $this->updateSubscriptionOrCreate($platform->id, $plan->id, $subscriber->id);

                $this->emailService->sendMailNewRegisterSubscriber($subscriber);

                return true;
            } else {
                $info = TmbSubscriber::fromArray($request);

                $subscriber = $this->createSubscriber($platform->id, $email, $info);

                $this->updateSubscriptionOrCreate($platform->id, $plan->id, $subscriber->id);

                $this->emailService->sendMailNewRegisterSubscriber($subscriber);

                return true;
            }
        } elseif ($status == self::TMB_STATUS_CANCELADO) {
            if (!$subscriber) {
                Log::warning('TMB: subscriber not found', [
                    'request' => $request,
                ]);

                return false;
            }

            $this->subscriptionService->cancelSubscription($subscriber, $plan, 'Cancelado pelo TMB');

            return true;
        }

        return false;
    }


    private function subscriberHasPlan($subscriberId, $planId): bool
    {
        return Subscription::where('subscriber_id', $subscriberId)
            ->where('plan_id', $planId)
            ->where('status', [
                Subscription::STATUS_ACTIVE,
            ])
            ->exists();
    }

    private function getSubscriberByPlatformAndEmail(string $platformId, string $email): ?Subscriber
    {
        return Subscriber::where('platform_id', $platformId)->where('email', $email)->first();
    }

    private function createSubscriber(string $platformId, string $email, TmbSubscriber $info): Subscriber
    {
        $subscriber = new Subscriber();
        $subscriber->name = $info->name;
        $subscriber->email = $email;
        $subscriber->cel_phone = $info->phone ?? null;
        $subscriber->created_at = Carbon::now()->format('Y-m-d H:i:s');
        $subscriber->status = Subscriber::STATUS_ACTIVE;
        $subscriber->address_street = $info->address ?? null;
        $subscriber->document_type = $info->document ? self::presumableDocumentType($info->document) : null;
        $subscriber->document_number = $info->document ? self::onlyDigits($info->document) : null;
        $subscriber->last_acess = null;
        $subscriber->plan_id = $info->planId;
        $subscriber->platform_id = $platformId;
        $subscriber->raw_password = keygen(12);
        $subscriber->source_register = Subscriber::SOURCE_TMB;
        $subscriber->save();

        return $subscriber;
    }

    private function updateSubscriptionOrCreate(string $platformId, string $planId, string $subscriberId): Subscription
    {
        return Subscription::updateOrCreate([
            'platform_id' => $platformId,
            'plan_id' => $planId,
            'subscriber_id' => $subscriberId,
        ], [
            'gateway_transaction_id' => '',
            'status' => Subscription::STATUS_ACTIVE,
        ]);
    }

    public static function onlyDigits(string $string): string
    {
        return preg_replace('/[^0-9]/', '', $string);
    }

    public static function presumableDocumentType(string $document): ?string
    {
        $stripped = self::onlyDigits($document);
        if (strlen($stripped) === self::CNPJ_LENGTH) {
            return 'CNPJ';
        } elseif (strlen($stripped) === self::CPF_LENGTH) {
            return 'CPF';
        }

        return null;
    }
}
