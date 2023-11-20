<?php

namespace App\Mail;

use App\Email;
use App\Payment;
use App\Subscriber;
use Carbon\CarbonImmutable;

/**
 * This email is sent when a recurrence payment is successfully done
 *
 * @package App\Mail
 */
class SendMailRecurrencePaymentSuccess extends BaseMail
{
    private $subscriber;
    private $payment;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($platformId, Subscriber $subscriber, Payment $payment) {
        parent::__construct($platformId, [$subscriber->email], Email::CONSTANT_EMAIL_RECURRENCE_PAYMENT_SUCCESS);
        $this->subscriber = $subscriber;
        $this->payment = $payment;
        $this->withVariables = [
            'supportText' => null,
        ];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() {
        $paymentType = $this->payment->type;

        $plans = ($paymentType === Payment::TYPE_SUBSCRIPTION)
            ? collect([$this->payment->recurrences[0]->plan])
            : $this->payment->plans;

        $products = $plans->implode('name', ', ');

        $installments = $this->payment->installments ?? 0;
        $installmentText = '';
        if ($installments > 0) {
            if ($paymentType == Payment::TYPE_SALE) {
                $valuePerInstallment = $this->payment->price / max(1, $installments);
                $value = formatCoin($valuePerInstallment);
                $installmentText = "Em {$installments} parcelas de {$value}";
            } elseif ($paymentType == Payment::TYPE_UNLIMITED) {
                $installmentNumber = $this->payment->installment_number;
                $value = formatCoin($this->payment->price * $installments);
                $installmentText = "Parcela paga: {$installmentNumber} de {$installments} (total de {$value})";
            }
        }

        $pricePaidByCustomer = formatCoin($this->payment->price);

        $paymentDate = new CarbonImmutable($this->payment->payment_date);

        $checkoutEmail = $plans->first()->checkout_email ?? '';

        $message = $this->template->message;
        $message = str_replace('##NOME_ASSINANTE##', $this->subscriber->name ?? '', $message);
        $message = str_replace('##EMAIL_ASSINANTE##', $this->subscriber->email ?? '', $message);
        $message = str_replace('##NOME_PLATAFORMA##', $this->platform->name ?? '', $message);
        $message = str_replace('##ID_TRANSACAO##', $this->payment->order_code ?? '', $message);
        $message = str_replace('##LINK_PLATAFORMA##', $this->platform->url ?? '', $message);
        $message = str_replace('##PRODUTOS##', $products ?? '', $message);
        $message = str_replace('##VALOR_PAGO##', $pricePaidByCustomer, $message);
        $message = str_replace('##PARCELAS##', $installmentText, $message);
        $message = str_replace('##DATA_COBRANCA##', dateBr($paymentDate), $message);
        $message = str_replace('##LINK_SUPORTE##', $checkoutEmail, $message);

        return $this->sendMail($this->template->subject, $message);
    }

}
