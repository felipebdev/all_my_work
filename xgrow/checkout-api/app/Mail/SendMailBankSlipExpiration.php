<?php

namespace App\Mail;

use App\Email;
use App\Payment;
use App\Subscriber;

class SendMailBankSlipExpiration extends BaseMail
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
        parent::__construct($platformId, [$subscriber->email], Email::CONSTANT_EMAIL_BANK_SLIP_EXPIRATION);
        $this->subscriber = $subscriber;
        $this->payment = $payment;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $products = "";
        $plans = ($this->payment->type === 'R') ? [$this->payment->recurrences[0]->plan] : $this->payment->plans;
        foreach ($plans as $plan) {
            $price = formatCoin($plan->price, $plan->currency);
            $products .= "{$plan->name} - {$price}<br>";
        }

        $pricePaidByCustomer = formatCoin($this->payment->price);

        $checkoutEmail = (is_array($plans)) ?
            $plans[0]->first()->checkout_email ?? null :
            $plans->first()->checkout_email ?? null;

        $supportText = '';
        if ($checkoutEmail) {
            $supportText = "Em caso de dúvida entre em contato com suporte: {$checkoutEmail}";
        }

        $message = $this->template->message;
        $message = str_replace('##NOME_ASSINANTE##', $this->subscriber->name ?? '', $message);
        $message = str_replace('##DATA_EMISSAO##', dateBr($this->payment->payment_date ?? ''), $message);
        $message = str_replace('##PRODUTOS##', $products ?? '', $message);
        $message = str_replace('##VALOR_PAGO##', $pricePaidByCustomer, $message);
        $message = str_replace('##BOLETO_URL##', $this->payment->boleto_url ?? '', $message);
        $message = str_replace('##MENSAGEM_SUPORTE##', $supportText, $message);

        return $this->sendMail($this->template->subject, $message);
    }

}
