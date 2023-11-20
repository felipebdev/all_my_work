<?php

namespace App\Repositories\Students;

use App\Exceptions\Finances\ActionFailedException;
use App\Exceptions\Finances\PaymentChange\PaymentChangeInvalidException;
use App\Exceptions\Finances\PaymentChange\PaymentChangeNotAllowedException;
use App\Exceptions\Students\OperationNotAllowedException;
use App\Exceptions\Students\RecurrenceNotFoundException;
use App\Payment;
use App\Platform;
use App\Recurrence;
use App\Services\Finances\Objects\Constants;
use App\Services\Finances\Payment\Manual\StudentPaymentService;
use App\Services\Finances\Transaction\GatewayTransaction;
use Carbon\Carbon;
use MundiAPILib\Models\GetOrderResponse;

class StudentsRecurrenceRepository
{
    private StudentPaymentService $studentPaymentService;

    public function __construct(StudentPaymentService $studentPaymentService)
    {
        $this->studentPaymentService = $studentPaymentService;
    }

    public function getStudentsRecurrenceById(int $recurrenceId): array
    {
        $data = Recurrence::select(
            'recurrences.id',
            'recurrences.last_payment',
            'recurrences.last_invoice',
            'recurrences.payment_method',
            'recurrences.total_charges',
            'recurrences.current_charge',
            'recurrences.order_number',
            'recurrences.recurrence',
            'recurrences.subscriber_id',
            'recurrences.plan_id'
        )
            ->find($recurrenceId);

        if (empty($data)) {
            throw new RecurrenceNotFoundException();
        }

        if ($data->payment_method == 'credit_card') {
            throw new OperationNotAllowedException();
        }


        $recurrence = [
            'id' => $data->id,
            'last_payment' => $data->last_payment,
            'last_invoice' => $data->last_invoice,
            'payment_method' => $data->payment_method,
            'total_charges' => $data->total_charges,
            'current_charge' => $data->current_charge,
            'order_number' => $data->order_number,
            'recurrence' => $data->recurrence
        ];

        $client = Platform::select('clients.first_name', 'clients.last_name')
            ->join('clients', 'clients.id', '=', 'platforms.customer_id')
            ->where('platforms.id', $data->plan->product->platform_id)
            ->first();

        $plan = [
            'id' => $data->plan->id,
            'name' => $data->plan->name,
            'description' => $data->plan->description,
            'recurrence' => $data->plan->recurrence,
            'status' => $data->plan->status,
            'price' => $data->plan->price,
            'freedays' => $data->plan->freedays,
            'freedays_type' => $data->plan->freedays_type,
            'installments' => $data->plan->installments,
            'payment_method_boleto' => $data->plan->payment_method_boleto,
            'payment_method_credit_card' => $data->plan->payment_method_credit_card,
            'payment_method_pix' => $data->plan->payment_method_pix,
            //'image' => $data->plan->image,
            'product' => [
                'name' => $data->plan->product->name,
                'type' => $data->plan->product->type,
                'status' => $data->plan->product->status,
                'image' => $data->plan->product->image->filename ?? '',
                'author' => $client->first_name . ' ' . $client->last_name
            ],

        ];

        $subscriber = [
            'name' => $data->subscriber->name,
            'email' =>  $this->obfuscateEmail($data->subscriber->email)
        ];

        $recurrence['plan'] = $plan;
        $recurrence['subscriber'] = $subscriber;

        return $recurrence;
    }

    /**
     * @param  int  $recurrenceId
     * @param  array  $request
     * @return \MundiAPILib\Models\GetOrderResponse
     * @throws \App\Exceptions\Finances\ActionFailedException
     * @throws \App\Exceptions\Finances\PaymentChange\PaymentChangeInvalidException
     * @throws \App\Exceptions\Finances\PaymentChange\PaymentChangeNotAllowedException
     * @throws \App\Exceptions\Students\RecurrenceNotFoundException
     */
    public function generateTransactionByRecurrence(int $recurrenceId, array $request): GetOrderResponse
    {
        $recurrence = Recurrence::where('id', $recurrenceId)->first();

        if (is_null($recurrence)) {
            throw new RecurrenceNotFoundException('Recorrencia não encontrada!');
        }

        if ($recurrence->payment_method == 'credit_card') {
            throw new PaymentChangeNotAllowedException('Esta recorrencia já tem por padrão cartão de crédito!');
        }

        if (!$this->canGenerateRecurrencePayment($recurrence)) {
            throw new PaymentChangeInvalidException('Não é possível gerar pagamento para essa recorrencia!');
        }

        $paymentMethod = $request['payment_method'];
        $ccInfo = ($paymentMethod == 'credit_card') ? $request['cc_info'] : [];
        $orderResponse = $this->studentPaymentService->createRecurrenceOrder($recurrence, $paymentMethod, $ccInfo);

        if (!$orderResponse) {
            throw new ActionFailedException('Falha no processo!');
        }

        if ($orderResponse->status != Constants::MUNDIPAGG_PAID) {
            $failures = GatewayTransaction::getOrderFailures($orderResponse);
            throw new ActionFailedException($failures[0]->friendly_message ?? 'Pagamento não autorizado');
        }

        return $orderResponse;
    }

    public function canGenerateRecurrencePayment(Recurrence $recurrence): bool
    {
        $nextPaymentDate = (new Carbon($recurrence->last_payment))->addDays($recurrence->recurrence);
        $back = $nextPaymentDate->clone()->subDays(4);
        $future = $nextPaymentDate->clone()->addDays(5);

        $payment = Payment::select('payments.id', 'payments.installment_number', 'payments.type_payment',
            'payments.expires_at', 'payments.status')
            ->join('payment_recurrence', 'payments.id', 'payment_recurrence.payment_id')
            ->where('payment_recurrence.recurrence_id', $recurrence->id)
            ->whereIn('payments.status', ['paid', 'pending'])
            ->when($recurrence->recurrence <> 1, function ($query) use ($back, $future) {
                $query->whereBetween('payments.payment_date', [$back->toDateString(), $future->toDateString()]);
            })
            ->when($recurrence->recurrence == 1, function ($query) use ($nextPaymentDate, $future) {
                $query->whereBetween('payments.payment_date',
                    [$nextPaymentDate->toDateString(), $future->toDateString()]);
            })
            ->orderBy('payments.created_at', 'DESC')
            ->first();

        if ($payment) {
            return false;
        }

        if (Carbon::now() >= $future) {
            return false;
        }

        return true;
    }

    public function obfuscateEmail($email): string
    {
        $em = explode("@", $email);
        $name = implode('@', array_slice($em, 0, count($em) - 1));
        $len = floor(strlen($name) / 2);

        return substr($name, 0, $len).str_repeat('*', $len)."@".end($em);
    }
}
