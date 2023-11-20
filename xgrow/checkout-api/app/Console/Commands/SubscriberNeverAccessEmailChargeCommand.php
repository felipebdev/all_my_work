<?php

namespace App\Console\Commands;

use App\ChargeRuler;
use App\Logs\XgrowLog;
use App\Notifications\SubscriberNotAccess;
use App\Platform;
use App\Services\ChargeRulerSettings;
use App\Subscriber;
use Carbon\CarbonImmutable;
use DateTimeInterface;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class SubscriberNeverAccessEmailChargeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "xgrow:charge-rules:never-access";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Launches subscribers never access charge rules';

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
        XgrowLog::mail()->info('### SUBSCRIBER NEVER ACCESS EMAIL CHARGE COMMAND START ###');

        try {
            $rules = ChargeRuler::where('platform_id', $platformId)
                ->where('active', true)
                ->where('type', ChargeRuler::TYPE_ACCESS)
                ->get();

            if ($rules->count() == 0) {
                $rules = ChargeRulerSettings::defaultNotificationsForAccess($platformId, $isActive = true);
            }

            foreach ($rules as $rule) {
                $startDate = CarbonImmutable::now()->subDays($rule->interval)->startOfDay();
                $endDate = CarbonImmutable::now()->subDays($rule->interval)->endOfDay();
                $subscribers = $this->listNeverAccessed($platformId, $startDate, $endDate);

                XgrowLog::xInfo("RuleID: {$rule->id} >", ['total_mails' => $subscribers->count()], 'mail');

                foreach ($subscribers as $subscriber) {
                    try {
                        Notification::route('mail', $subscriber->email)->notify(
                            new SubscriberNotAccess($subscriber, $subscriber->plan)
                        );
                    } catch (Exception $e) {
                        $this->logException($e, $subscriber);
                    }
                }
            }
        } catch (Exception $e) {
        }

        XgrowLog::mail()->info('### SUBSCRIBER NEVER ACCESS EMAIL CHARGE COMMAND FINISHED ###');
    }

    /**
     * List subscribers that never accessed the platform
     *
     * @param  string  $platformId
     * @param  \DateTimeInterface|null  $begin  List only newer than $begin (null for no start date)
     * @param  \DateTimeInterface|null  $end  List only older than $end (null for no ending date)
     * @return array|\Illuminate\Database\Concerns\BuildsQueries[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    private function listNeverAccessed(string $platformId, ?DateTimeInterface $begin, ?DateTimeInterface $end)
    {
        return Subscriber::with([
            'plan' => function ($query) {
                return $query->select('id', 'name as planName');
            }
        ])
            ->whereHas('platform', function ($query) use ($platformId) {
                $query->where('id', '=', $platformId);
            })
            ->whereNull('last_acess')
            ->when($begin, function ($query, $begin) {
                $query->where('created_at', '>=', $begin);
            })
            ->when($end, function ($query, $end) {
                $query->where('created_at', '<=', $end);
            })
            ->get();
    }

    private function logException(Exception $e, $subscriber)
    {
        XgrowLog::xError('Can not send charge rule email', $e, [
            'platform' => $subscriber->platform->id ?? null,
            'subscriber' => [
                'name' => $subscriber->name ?? null,
                'email' => $subscriber->email ?? null,
            ],
        ], 'mail');
    }
}
