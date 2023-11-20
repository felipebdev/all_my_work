<?php

namespace Modules\Integration\Services;

use App\Payment;
use App\Plan;
use App\Platform;
use App\PlatformUser;
use Modules\Integration\Enums\ActionEnum;
use Modules\Integration\Enums\CodeEnum;
use Modules\Integration\Enums\EventEnum;
use Modules\Integration\Enums\TypeEnum;
use Modules\Integration\Models\Action;
use Modules\Integration\Models\Integration;
use Modules\Integration\Repositories\MobileNotificationRepository;
use Modules\Integration\Services\Objects\ExpoMessage;
use NumberFormatter;
use stdClass;

use function array_first;

class MobileNotificationService
{
    private bool $includesPlanName = true;

    private MobileNotificationRepository $mobileNotificationRepository;

    public function __construct(MobileNotificationRepository $mobileNotificationRepository)
    {
        $this->mobileNotificationRepository = $mobileNotificationRepository;
    }

    public function includesPlanName(bool $includesPlanName = true): self
    {
        $this->includesPlanName = $includesPlanName;
        return $this;
    }

    public function generateExpoNotificationAction(
        string $event,
        string $platformId,
        array $plansId,
        Payment $payment
    ): ?Action {
        $first = array_first($plansId);
        $id = $first['id'] ?? $first;

        $mainPlan = Plan::find($id);

        $message = $this->getExpoMessage($event, $mainPlan, $payment);

        if (is_null($message)) {
            return null; // no message defined, return null (no action)
        }

        $platformUsers = Platform::find($platformId)->users;

        // filter destination users
        $platformUsersWithNotification = $platformUsers->filter(function ($user, $key) {
            $hasToken = $user->expo_push_token != null;

            return $hasToken;
        })->filter(function ($user, $key) {
            $hasGlobalNotificationEnabled = $user->mobile_configuration->notifications ?? true;

            return $hasGlobalNotificationEnabled;
        })->filter(function ($user, $key) {
            $hasSaleNotificationEnabled = $user->mobile_configuration->notifications_sells ?? true;

            return $hasSaleNotificationEnabled;
        })->filter(function ($user, $key) {
            $wantsPlanName = $user->mobile_configuration->notifications_sells_product_name ?? true;

            return $wantsPlanName == $this->includesPlanName;
        });

        if ($platformUsersWithNotification->count() == 0) {
            return null;
        }

        // store each user's notification on DB
        $platformUsersWithNotification->each(function (PlatformUser $platformUser) use ($platformId, $event, $message) {
            $this->mobileNotificationRepository->saveNotification($platformId, $platformUser->id, $message);
        });

        // get tokens only
        $expoTokens = $platformUsersWithNotification->map(function (PlatformUser $platformUser) {
            return $platformUser->expo_push_token;
        });

        // create in-memory model
        $expoAction = $this->createInMemoryModel($platformId, $event, $message, $expoTokens);

        return $expoAction;
    }

    /**
     * Get ExpoMessage to be sent based on parameters
     *
     * @param  string  $event
     * @param  \App\Plan  $plan
     * @param  \App\Payment  $payment
     * @return \Modules\Integration\Services\Objects\ExpoMessage|null
     */
    private function getExpoMessage(string $event, Plan $plan, Payment $payment): ?ExpoMessage
    {
        $planName = $this->includesPlanName ? " - {$plan->name}" : '';

        if ($event == EventEnum::BANK_SLIP_CREATED) {
            return new ExpoMessage(
                'Novo Boleto Gerado!',
                "Valor: {$this->format($payment->price)}{$planName}"
            );
        }

        if ($event == EventEnum::PAYMENT_APPROVED) {
            return new ExpoMessage(
                "Pagamento recebido! #{$plan->order_number}",
                "Sua comissão: {$this->format($payment->customer_value)}{$planName}"
            );
        }

        return null;
    }

    private function format(string $value): string
    {
        $amount = (float) $value;

        $formatter = new NumberFormatter('pt_BR', NumberFormatter::CURRENCY);

        return $formatter->formatCurrency($amount, 'BRL');
    }

    /**
     * Create an in-memory Model.
     *
     * A Model is required due to internals of Integration system.
     *
     * @param  string  $platformId
     * @param  string  $event
     * @param  \Modules\Integration\Services\Objects\ExpoMessage  $message
     * @param $expoTokens
     * @return \Modules\Integration\Models\Action
     */
    private function createInMemoryModel(string $platformId, string $event, ExpoMessage $message, $expoTokens): Action
    {
        $expoAction = new Action([
            'app_id' => CodeEnum::EXPO,
            'platform_id' => $platformId,
            'is_active' => true,
            'description' => 'EXPO integration',
            'event' => $event,
            'action' => ActionEnum::TRIGGER_EXPO,
            'metadata' => [],
        ]);

        $integration = new Integration([
            'id' => CodeEnum::EXPO,
            'type' => TypeEnum::EXPO,
            'metadata' => [
                'messageTitle' => $message->title,
                'messageBody' => $message->body,
                'expoTokens' => array_values($expoTokens->toArray()), // send same message to all users
                'messageData' => new stdClass(), // object
            ],
        ]);

        // set relation on these in-memory models
        $expoAction->setRelation('integration', $integration);

        return $expoAction;
    }

}
