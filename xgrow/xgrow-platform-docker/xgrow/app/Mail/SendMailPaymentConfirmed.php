<?php

namespace App\Mail;

use App\Email;
use App\Payment;
use App\Subscriber;

/**
 * Class SendMailPaymentConfirmed
 *
 * @package App\Mail
 */
class SendMailPaymentConfirmed extends BaseMail
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
        parent::__construct($platformId, [$subscriber->email], Email::CONSTANT_EMAIL_PAYMENT_CONFIRMED);
        $this->subscriber = $subscriber;
        $this->payment = $payment;
    }

    /**
     * This implementation uses a fixed template instead of template from DB
     *
     * @override BaseMail::setTemplate()
     *
     * @param $emailId
     * @param  null  $platformId
     * @return \App\Mail\BaseMail|void
     */
    protected function setTemplate($emailId, $platformId = null)
    {
        $template = new \stdClass();
        $template->subject = 'Confirmação de pagamento';
        $template->message = '##EMAIL_ASSINANTE##, seu pagamento foi confirmado';
        $template->message = "Olá ##NOME_ASSINANTE##,<br><br>

PARABÉNS!!!<br><br>

Segue abaixo os dados de confirmação da sua compra.<br><br>

Dados do comprador:<br>
Nome: ##NOME_ASSINANTE##<br>
##TIPO_DOCUMENTO_ASSINANTE##: ##NUMERO_DOCUMENTO_ASSINANTE##<br>
E-mail: ##EMAIL_ASSINANTE##<br>
Celular: ##CELULAR_ASSINANTE##<br><br>

Código da Compra:<br>
##CODIGO_COMPRA##<br><br>

Dados da compra:<br>
##PRODUTOS##<br><br>

Valor Pago:<br>
##VALOR_PAGO##<br>
##PARCELAS##<br><br>

##MENSAGEM_SUPORTE##<br><br>

Abraços,<br>
Equipe Xgrow.";

        $this->template = $template;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $products = "";
        $paymentType = $this->payment->type;

        $plans = ($paymentType === Payment::TYPE_SUBSCRIPTION)
            ? collect([$this->payment->recurrences[0]->plan])
            : $this->payment->plans;

        $mainPlan = $plans->first();

        if( $mainPlan ) {
            if( $mainPlan->product ) {
                $product = $mainPlan->product;
                $this->setHeaderName($product->name);
                $productSupportEmail = $product->support_email;
            }
        }

        foreach ($plans as $plan) {
            $price = formatCoin($plan->price, $plan->currency);
            $products .= "{$plan->name} - {$price}<br>";
        }

        $subject = $this->template->subject;
        $message = $this->template->message;

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
                $installmentText = "Parcela {$installmentNumber} de {$installments} (total de {$value})";
            }
        }

        $pricePaidByCustomer = formatCoin($this->payment->price);

        $checkoutEmail = $productSupportEmail ?? $mainPlan->checkout_email ?? null;

        $supportText = '';
        if ($checkoutEmail) {
            $supportText = "Para dúvidas relacionada ao produto, entre em contato com o produtor em: {$checkoutEmail}";
        }

        $message = str_replace('##NOME_ASSINANTE##', $this->subscriber->name ?? '', $message);
        $message = str_replace('##EMAIL_ASSINANTE##', $this->subscriber->email ?? '', $message);
        $message = str_replace('##NOME_PLATAFORMA##', $this->platform->name ?? '', $message);
        $message = str_replace('##LINK_PLATAFORMA##', $this->platform->url ?? '', $message);
        $message = str_replace('##TIPO_DOCUMENTO_ASSINANTE##', strtoupper($this->subscriber->document_type) ?? '', $message);
        $message = str_replace('##NUMERO_DOCUMENTO_ASSINANTE##', $this->subscriber->document_number ?? '', $message);
        $message = str_replace('##CELULAR_ASSINANTE##', $this->subscriber->cel_phone ?? '', $message);
        $message = str_replace('##PRODUTOS##', $products ?? '', $message);
        $message = str_replace('##VALOR_PAGO##', $pricePaidByCustomer, $message);
        $message = str_replace('##PARCELAS##', $installmentText, $message);
        $message = str_replace('##CODIGO_COMPRA##', $this->payment->order_code ?? '', $message);
        $message = str_replace('##MENSAGEM_SUPORTE##', $supportText, $message);

        return $this->sendMail($subject, $message);
    }

}
