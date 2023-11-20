<?php

namespace App\Mail;

use App\Email;
use App\Payment;
use App\Subscriber;

class SendMailBankSlip extends BaseMail
{
    private $platformId;
    private $subscriber;
    private $payment;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($platformId, Subscriber $subscriber, Payment $payment) {
        parent::__construct($platformId, [$subscriber->email], Email::CONSTANT_EMAIL_BOLETO);
        $this->subscriber = $subscriber;
        $this->payment = $payment;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() {
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
        $message = str_replace('##EMAIL_ASSINANTE##', $this->subscriber->email ?? '', $message);
        $message = str_replace('##NOME_PLATAFORMA##', $this->platform->name ?? '', $message);
        $message = str_replace('##TIPO_DOCUMENTO_ASSINANTE##', strtoupper($this->subscriber->document_type) ?? '', $message);
        $message = str_replace('##NUMERO_DOCUMENTO_ASSINANTE##', $this->subscriber->document_number ?? '', $message);
        $message = str_replace('##CELULAR_ASSINANTE##', $this->subscriber->cel_phone ?? '', $message);
        $message = str_replace('##CODIGO_COMPRA##', $this->payment->order_code ?? '', $message);
        $message = str_replace('##PRODUTOS##', $products ?? '', $message);
        $message = str_replace('##VALOR_PAGO##', $pricePaidByCustomer, $message);
        $message = str_replace('##BOLETO_URL##', $this->payment->boleto_url ?  $this->getLink($this->payment->order_code) : '', $message);
        $message = str_replace('##MENSAGEM_SUPORTE##', $supportText, $message);

        return $this->sendMail($this->template->subject, $message);
    }

    private function getLink($order_code)
    {
        $url = route('checkout.boleto.download', $order_code);
        return "<a href='{$url}' target='_blank'>Clique aqui</a>";
    }

}
