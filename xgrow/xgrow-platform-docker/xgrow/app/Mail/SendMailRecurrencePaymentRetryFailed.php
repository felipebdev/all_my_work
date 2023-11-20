<?php

namespace App\Mail;

use App\Email;
use App\Payment;
use App\Subscriber;
use Carbon\CarbonImmutable;

/**
 * This mail is sent when a retry payment fails
 *
 * @package App\Mail
 */
class SendMailRecurrencePaymentRetryFailed extends BaseMail
{
    private $subscriber;
    private $originalFailedPayment;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($platformId, Subscriber $subscriber, Payment $originalFailedPayment)
    {
        $this->subscriber = $subscriber;
        $this->originalFailedPayment = $originalFailedPayment;
        parent::__construct(
            $platformId,
            [$subscriber->email],
            Email::CONSTANT_EMAIL_RECURRENCE_PAYMENT_RETRY_FAILED
        );
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $plans = ($this->originalFailedPayment->type === Payment::TYPE_SUBSCRIPTION)
            ? collect([$this->originalFailedPayment->recurrences[0]->plan])
            : $this->originalFailedPayment->plans;

        $products = $plans->implode('name', ', ');

        $paymentDate = CarbonImmutable::now();

        $checkoutEmail = $plans->first()->checkout_email ?? '';

        $message = $this->template->message;
        $message = str_replace('##NOME_ASSINANTE##', $this->subscriber->name ?? '', $message);
        $message = str_replace('##PRODUTOS##', $products ?? '', $message);
        $message = str_replace('##DATA_COBRANCA##', dateBr($paymentDate) ?? '', $message);
        $message = str_replace('##LINK_SUPORTE##', $checkoutEmail, $message);

        return $this->sendMail($this->template->subject, $message);
    }
}
