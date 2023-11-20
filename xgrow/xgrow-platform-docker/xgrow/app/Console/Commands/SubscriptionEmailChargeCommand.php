<?php

namespace App\Console\Commands;

use App\ChargeRuler;
use App\Logs\ChargeLog;
use App\Logs\XgrowLog;
use App\Mail\FactoryMail;
use App\Mail\Objects\MailPayload;
use App\Payment;
use App\Platform;
use App\Services\ChargeRulerSettings;
use App\Services\Charges\SubscriptionChargeService;
use Carbon\CarbonImmutable;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class SubscriptionEmailChargeCommand extends Command
{
    protected $signature = 'xgrow:charge-rules:subscription'.
    '{--platform_id= : Restrict to single platform}'.
    '{--subscriber_id= : Restrict to single subscriber}'.
    '{--payment_id= : Restrict to single payment}'.
    '{--dry-run : Run in test mode (no real transaction)}'.
    '{--allow-default-settings : Allow use of default charge ruler settings}'.
    '{--base-date= : Use this as a base date (Y-m-d format)}';

    protected $description = 'Launches subscription charge rules';

    /**
     * @var SubscriptionChargeService
     */
    private $subscriptionChargeService;

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(SubscriptionChargeService $subscriptionChargeService)
    {
        Config::set('command_correlation_id', (string) Str::uuid());
        ChargeLog::info('Subscription retry command starting');

        $this->subscriptionChargeService = $subscriptionChargeService;

        $restrictPlatform = $this->option('platform_id');
        $platforms = is_null($restrictPlatform) ? Platform::all() : Platform::where('id', $restrictPlatform)->get();

        foreach ($platforms as $platform) {
            $this->processSinglePlatform($platform->id);
        }

        ChargeLog::info('Subscription retry command finished for all platforms');
    }

    private function processSinglePlatform(string $platformId)
    {
        ChargeLog::info('Subscription retry command starting for single platform', ['platform_id' => $platformId]);

        try {
            $rules = ChargeRuler::where('platform_id', $platformId)
                ->where('active', true)
                ->where('type', ChargeRuler::TYPE_SUBSCRIPTION)
                ->get();

            $allowDefaultSettings = $this->option('allow-default-settings');
            if ($rules->count() == 0 && $allowDefaultSettings) {
                ChargeLog::info('Subscription retry command using default charge rules', ['platform_id' => $platformId]);
                $rules = ChargeRulerSettings::defaultChargesForSubscription($platformId, $isActive = true);
            }

            foreach ($rules as $rule) {
                $baseDate = $this->option('base-date');
                $date = $baseDate ? CarbonImmutable::createFromFormat('Y-m-d', $baseDate) : CarbonImmutable::now();

                $paymentDate = $date->subDays($rule->interval)->toDateString();
                $payments = $this->listFailedPayment($platformId, $paymentDate);

                ChargeLog::info('Subscription retry processing rule', [
                    'platform_id' => $platformId ?? null,
                    'rule_id' => $rule->id ?? null,
                    'total_affected' => $payments->count() ?? null,
                    'rule_interval' => $rule->interval ?? null,
                ]);

                foreach ($payments as $payment) {
                    try {
                        $mailPayload = new MailPayload($payment->platform_id, [
                            'subscriber' => $payment->subscriber,
                            'payment' => $payment
                        ]);

                        $mail = FactoryMail::build($rule->email_id, $mailPayload);
                        $cancelOnFail = ChargeRulerSettings::isCancelRequired($rule->email_id);

                        $dryRun = $this->option('dry-run');
                        $this->subscriptionChargeService->retryChargePayment(
                            $payment,
                            $mail,
                            $cancelOnFail,
                            $dryRun,
                            $baseDate
                        );
                    } catch (Exception $e) {
                        $this->logException($e, $payment);
                    }
                }
            }
        } catch (Exception $e) {
            report($e);
        }

        ChargeLog::info('Subscription email charge command finished for single platform', [
            'platform_id' => $platformId,
        ]);
    }

    /**
     * List subscriptions with failed credit card payments with payment date in a given date
     *
     * @param  string  $platformId
     * @param  string  $paymentDate  Payment date on YYYY-MM-DD format
     * @return array|\Illuminate\Database\Concerns\BuildsQueries[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    private function listFailedPayment(string $platformId, string $paymentDate)
    {
        $subscriberId = $this->option('subscriber_id');
        $paymentId = $this->option('payment_id');

        return Payment::with(['platform', 'subscriber'])
            ->where('platform_id', $platformId)
            ->where('type', Payment::TYPE_SUBSCRIPTION)
            ->where('type_payment', Payment::TYPE_PAYMENT_CREDIT_CARD)
            ->whereIn('status', [Payment::STATUS_FAILED])
            ->where('payment_date', $paymentDate)
            ->whereNull('payment_id') // exclude retries
            ->when($subscriberId, function ($query, $subscriberId) {
                $query->where('subscriber_id', $subscriberId);
            })
            ->when($paymentId, function ($query, $paymentId) {
                $query->where('id', $paymentId);
            })
            ->get();
    }

    private function logException(Exception $e, $payment)
    {
        XgrowLog::xError('Can not send charge rule email', $e, [
            'platform' => $payment->platform->id ?? null,
            'payment' => $payment->id ?? null,
            'subscriber' => [
                'name' => $payment->subscriber->name ?? null,
                'email' => $payment->subscriber->email ?? null,
            ],
        ], 'mail');
    }
}
