<?php

namespace App\Http\Controllers\Mundipagg;

use App\Http\Controllers\Controller;
use App\Jobs\MundipaggRecurrenceOrder;
use App\Jobs\MundipaggUnlimitedOrder;
use App\Logs\XgrowLog;
use App\Payment;
use App\Recurrence;
use App\Services\Charges\SubscriptionChargeService;
use App\Services\Contracts\SubscriptionServiceInterface;
use App\Subscriber;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RecurrenceController extends Controller
{
    private $subscriptionService;

    /**
     * @var $subscriptionChargeService SubscriptionChargeService
     */
    private $subscriptionChargeService;

    public function __construct()
    {
        $this->subscriptionService = app()->make(SubscriptionServiceInterface::class);
        $this->subscriptionChargeService = app()->make(SubscriptionChargeService::class);
    }

    /**
     * Create a recurrence for a subscription
     * @param $subscriber_id
     * @param $recurrence
     * @param $paid_at
     * @param $credit_card_id
     * @param int $current_charge
     * @param string $type
     * @param null $total_charges
     * @return Recurrence
     */
    public function store(
        $subscriber_id, 
        $recurrence, 
        $paid_at, 
        $credit_card_id, 
        $plan_id, 
        $current_charge = 1, 
        $type = Recurrence::TYPE_SUBSCRIPTION, 
        $total_charges = null,
        $orderNumber = null
    ) {
        $objRecurrence = new Recurrence();
        $objRecurrence->subscriber_id = $subscriber_id;
        $objRecurrence->recurrence = $recurrence;
        $objRecurrence->last_payment = $paid_at;
        $objRecurrence->card_id = $credit_card_id;
        $objRecurrence->current_charge = $current_charge;
        $objRecurrence->type = $type;
        $objRecurrence->total_charges = $total_charges;
        $objRecurrence->plan_id = $plan_id;
        $objRecurrence->order_number = $orderNumber;
        $objRecurrence->save();

        return $objRecurrence;
    }

    public function process()
    {
        $this->processRecurrences();
        $this->processUnlimitedSell();
    }

    public function processRecurrences()
    {   
        Log::info('### Process Recurrence payments ###');
        $recurrences = Recurrence::
            selectRaw('
                DISTINCT MAX(recurrences.id) AS id,
                recurrences.subscriber_id,
                recurrences.recurrence,
                MAX(recurrences.last_payment) AS last_payment,
                MAX(recurrences.current_charge) AS current_charge,
                recurrences.type,
                MAX(recurrences.total_charges) AS total_charges,
                recurrences.plan_id,
                MAX(recurrences.order_number) AS order_number
            ')
            ->leftJoin('subscriptions', function ($query) {
                $query->on('subscriptions.subscriber_id', '=', 'recurrences.subscriber_id')
                    ->on('subscriptions.plan_id', '=', 'recurrences.plan_id');
            })
            ->whereNull('subscriptions.canceled_at')
            ->whereNull('subscriptions.payment_pendent')
            ->groupBy('subscriptions.id')
            ->get();

        foreach ($recurrences as $cod => $recurrence) {
            $this->subscriptionChargeService->dispatchSingleRecurrence($recurrence);
        }
    }

    /**
     * Process Unlimited order pending payments
     */
    public function processUnlimitedSell()
    {
        Log::info('### Process Unlimited order pending payments ###');
        $payments = Payment::select(
                'payments.id', 
                'payments.status', 
                'payments.payment_date', 
                'payments.subscriber_id', 
                'payments.installment_number',
                'payments.order_number'
            )
            ->with([
                'plans' => function ($query) {
                    return $query->select('plans.id');
                }
            ])
            ->whereDate('payment_date', '=', Carbon::now()->toDate())
            ->where('type', '=', Payment::TYPE_UNLIMITED)
            ->where(function ($query) {
                $query->where('status', '=', Payment::STATUS_PENDING);
            })
            ->get();
        
        $uniqueId = null;
        $paymentsToProcess = [];
        foreach ($payments as $key => $value) {
            if (!empty($value->order_number)) {
                if (!array_key_exists($value->order_number, $paymentsToProcess)) {
                    $paymentsToProcess[$value->order_number] = [];
                }

                array_push(
                    $paymentsToProcess[$value->order_number], 
                    $value
                );
            }
            else {
                if ($key === 0 || 
                    $value->subscriber_id !== $payments[$key-1]->subscriber_id || // se alunos diferentes: novo sem limite
                    $value->installment_number < $payments[$key-1]->installment_number || //se nº parcela for menor do que anterior: novo sem limite
                    !($value->plans->diff($payments[$key-1]->plans)->isEmpty()) //se produtos diferentes: novo sem limite
                ) {
                    $uniqueId = strtoupper(uniqid());
                    $paymentsToProcess[$uniqueId] = [$value];
                }
                else {
                    array_push(
                        $paymentsToProcess[$uniqueId], 
                        $value
                    );
                }
            }
        }
        
        Log::info('>>> Total payments grouped: '.count($paymentsToProcess));
        foreach ($paymentsToProcess as $key => $value)  {
            if (!in_array_field('canceled', 'status', $value)) {
                foreach ($value as $payment) {
                    $hasActiveSubscription = true;
                    foreach($payment->plans as $cod => $plan) {
                        $hasActiveSubscription &= $this->subscriptionService->hasActiveSubscription(
                            $payment->subscriber->id,
                            $payment->subscriber->platform_id,
                            $plan->id
                        );
                    }
                    if($payment->subscriber->status == Subscriber::STATUS_ACTIVE && 
                        $hasActiveSubscription
                    ) {
                        Log::info('Payment: ', [
                            'id' => $payment->id, 
                            'subscriber_id' => $payment->subscriber_id, 
                            'status' => $payment->status,
                            'payment_date' => $payment->payment_date,
                            'installment_number' => $payment->installment_number,
                            'order_number' => $payment->order_number,
                            'plans' => $payment->plans->implode('id', ',')
                        ]);

                        $this->dispatch(new MundipaggUnlimitedOrder($payment));
                    }
                }
            }
        }
    }
}
