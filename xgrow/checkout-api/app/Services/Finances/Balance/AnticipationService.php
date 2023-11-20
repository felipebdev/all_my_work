<?php

namespace App\Services\Finances\Balance;

use App\Exceptions\NotImplementedException;
use App\PaymentPlanSplit;
use App\Repositories\Finances\RecipientRepository;
use App\Services\Finances\Recipient\Contracts\RecipientManagerInterface;
use App\Services\Finances\Recipient\RecipientManagerAdapter;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use stdClass;

/**
 * Check settings on payment gateway to acquire the anticipation delay and calculate anticipation amount using the
 * database. Uses Cache to prevent rate limiting.
 */
class AnticipationService
{
    private RecipientManagerInterface $recipientManager;

    public function __construct(RecipientManagerAdapter $managerAdapter)
    {
        $managerDriver = $managerAdapter->driver(RecipientManagerAdapter::DRIVER_PAGARME_V4); // use Pagarme V4 driver
        if (!$managerDriver instanceof RecipientManagerInterface) {
            throw new NotImplementedException('Recipient manager not implemented by driver: '.$managerAdapter->getDefaultDriver());
        }

        $this->recipientManager = $managerDriver;
    }

    public function getAnticipationAmount(string $platformId, stdClass $recipient): int
    {
        $anticipationDelay = $this->rememberAnticipationDelay($recipient->id);

        if ($recipient->type === RecipientRepository::RECIPIENT_TYPE_CLIENT) {
            $anticipationAmount = $this->getAnticipationAmountForClient($platformId, $anticipationDelay + 1);
        } elseif ($recipient->type === RecipientRepository::RECIPIENT_TYPE_PRODUCER) {
            $anticipationAmount = $this->getAnticipationAmountForProducer($platformId, $anticipationDelay + 1);
        } else {
            $anticipationAmount = 0;
        }
        return $anticipationAmount;
    }

    /**
     * Get anticipation amount for a client (producer).
     *
     * @param  string  $platformId
     * @param  int  $days
     * @return int
     */
    protected function getAnticipationAmountForClient(string $platformId, int $days = 30): int
    {
        return $this->getAnticipationAmountForType($platformId, PaymentPlanSplit::SPLIT_TYPE_CLIENT, $days);
    }

    /**
     * Get anticipation amount for a producer (co-producer).
     *
     * @param  string  $platformId
     * @param  int  $days
     * @return int
     */
    protected function getAnticipationAmountForProducer(string $platformId, int $days = 30): int
    {
        return $this->getAnticipationAmountForType($platformId, PaymentPlanSplit::SPLIT_TYPE_PRODUCER, $days);
    }

    private function getAnticipationAmountForType(string $platformId, string $type, int $days): int
    {
        $anticipationValue = DB::table('payment_plan_split')
            ->join('payment_plan', 'payment_plan.id', '=', 'payment_plan_split.payment_plan_id')
            ->join('payments', 'payments.id', '=', 'payment_plan.payment_id')
            ->where('payments.payment_date', '>=', Carbon::now()->subDays($days))
            ->where('payments.type', '!=', 'U')
            ->where('payments.status', '=', 'paid')
            ->where('payments.platform_id', '=', $platformId)
            ->where('payment_plan_split.type', '=', $type)
            ->sum('payment_plan_split.anticipation_value');

        return round(100 * $anticipationValue);
    }

    /**
     * Get anticipation delay of a recipient on Gateway and stores in cache (due to rate limits).
     *
     * @param $recipientId
     * @return int Delay in days.
     */
    private function rememberAnticipationDelay($recipientId): int
    {
        $key = "checkout:financial:anticipation_delay:{$recipientId}";

        return Cache::remember($key, 60, fn() => $this->recipientManager->getAnticipationDelay($recipientId));
    }


}
