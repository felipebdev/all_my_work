<?php

namespace App\Mail;

use App\Email;
use App\Payment;
use App\Subscriber;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Log;

/**
 * This mail is sent when "first" recurrence payment fails
 *
 * @package App\Mail
 */
class SendMailRecurrencePaymentFailed extends BaseMail
{
    private $subscriber;
    private $payment;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($platformId, Subscriber $subscriber, Payment $payment)
    {
        $this->subscriber = $subscriber;
        $this->payment = $payment;
        parent::__construct($platformId, [$subscriber->email], Email::CONSTANT_EMAIL_RECURRENCE_PAYMENT_FAILED);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if (($this->payment->type === Payment::TYPE_SUBSCRIPTION)) {
            $recurrences = $this->payment->recurrences ?? null;

            if (!isset($recurrences[0])) {
                Log::error('Recurrence not found for payment', [
                    'payment_id' => $this->payment->id ?? '',
                    'payment_type' => $this->payment->type ?? '',
                ]);
                throw new \Exception("Recurrence not found for payment {$this->payment->id}");
            }

            $plans = collect([$recurrences[0]->plan]);
        } else {
            $plans = $this->payment->plans;
        }

        $products = $plans->implode('name', ', ');

        $checkoutEmail = $plans->first()->checkout_email ?? '';
        $paymentDate = new CarbonImmutable($this->payment->payment_date);
        // $nextChargeDate = $paymentDate->addDay();

        $message = $this->template->message;
        $message = str_replace('##NOME_ASSINANTE##', $this->subscriber->name ?? '', $message);
        $message = str_replace('##PRODUTOS##', $products ?? '', $message);
        $message = str_replace('##DATA_COBRANCA##', dateBr($paymentDate) ?? '', $message);
        // $message = str_replace('##DATA_NOVA_COBRANCA##', dateBr($nextChargeDate) ?? '', $message);
        $message = str_replace('##LINK_PLATAFORMA##', $this->platform->url ?? '', $message);
        $message = str_replace('##LINK_SUPORTE##', $checkoutEmail, $message);

        return $this->sendMail($this->template->subject, $message);
    }
}