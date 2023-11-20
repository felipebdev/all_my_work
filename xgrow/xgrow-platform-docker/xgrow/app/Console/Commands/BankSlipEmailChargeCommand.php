<?php

namespace App\Console\Commands;

use App\ChargeRuler;
use App\Logs\XgrowLog;
use App\Mail\FactoryMail;
use App\Mail\Objects\MailPayload;
use App\Payment;
use App\Platform;
use App\Services\ChargeRulerSettings;
use App\Services\EmailService;
use Carbon\CarbonImmutable;
use DateTimeInterface;
use Exception;
use Illuminate\Console\Command;

class BankSlipEmailChargeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "xgrow:charge-rules:bank-slip";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Launches bank slip charge rules';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        foreach (Platform::all() as $platform) {
            $this->processSinglePlatform($platform->id);
        }
    }

    private function processSinglePlatform(string $platformId)
    {
        XgrowLog::mail()->info('### BANK SLIP EMAIL CHARGE COMMAND START ###');

        try {
            $rules = ChargeRuler::where('platform_id', $platformId)
                ->where('active', true)
                ->where('type', ChargeRuler::TYPE_BOLETO)
                ->get();

            if ($rules->count() == 0) {
                // use all default rules as active rules
                $rules = ChargeRulerSettings::defaultNotificationsForBoleto($platformId, $isActive = true);
            }

            foreach ($rules as $rule) {
                $paymentDate = CarbonImmutable::now()->subDays($rule->interval)->toDateString();
                $payments = $this->listPendingPayments($platformId, $paymentDate);

                XgrowLog::xInfo("RuleID: {$rule->id} >", ['total_mails' => $payments->count()], 'mail');

                foreach ($payments as $payment) {
                    try {
                        $mailPayload = new MailPayload($payment->platform_id, [
                            'subscriber' => $payment->subscriber,
                            'payment' => $payment
                        ]);

                        $mail = FactoryMail::build($rule->email_id, $mailPayload);
                        EmailService::mail([$payment->subscriber->email], $mail);
                    } catch (Exception $e) {
                        $this->logException($e, $payment);
                    }
                }
            }
        } catch (Exception $e) {
        }

        XgrowLog::mail()->info('### BANK SLIP EMAIL CHARGE COMMAND FINISHED ###');
    }

    /**
     * List all pending "boletos" created in a given period
     *
     * @param  string  $platformId
     * @param  string  $paymentDate
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    private function listPendingPayments(string $platformId, string $paymentDate)
    {
        return Payment::with(['platform', 'subscriber'])
            ->where('platform_id', $platformId)
            ->where('type', Payment::TYPE_SALE)
            ->where('type_payment', Payment::TYPE_PAYMENT_BILLET)
            ->whereIn('status', [Payment::STATUS_PENDING])
            ->where('payment_date', $paymentDate)
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
