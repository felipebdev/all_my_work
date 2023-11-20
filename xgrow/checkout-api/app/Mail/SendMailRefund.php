<?php

namespace App\Mail;

use App\Email;
use App\Payment;
use App\PaymentPlan;
use App\Subscriber;
use App\Mail\BaseMail;
use Illuminate\Support\Facades\Log;

class SendMailRefund extends BaseMail
{
    private $subscriber;
    private $payment;
    private $paymentPlan;
    private $refundCode;
    private $refundValue;
    private $planValue;
    private $message = <<< EOT
Olá, ##NOME_ASSINANTE##.
<br /><br />
Recebemos sua solicitação de Reembolso referente ao produto ##PRODUTOS## no valor de R$ ##VALOR_PRODUTO##
 <br /><br />
Como a compra foi realizada com “ Cartão, boleto ou pix” o valor aparecerá na atual ou próxima Fatura”.
 <br /><br />
Informações da compra:<br />
Nome do Produto: ##PRODUTOS##<br />
Valor: R$ ##VALOR_ESTORNO##<br />
Data: ##DATA##<br />
Código da transação: ##CODIGO_ESTORNO##<br />
<br /><br />
Atenciosamente<br />
Equipe Xgrow<br />
EOT;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($platformId, Subscriber $subscriber, Payment $payment, $refundCode, $refundValue, $planValue, ?PaymentPlan $paymentPlan = null) {
        parent::__construct($platformId, [$subscriber->email], Email::CONSTANT_EMAIL_REFUND);
        $this->subscriber = $subscriber;
        $this->payment = $payment;
        $this->paymentPlan = $paymentPlan;
        $this->refundCode = $refundCode;
        $this->refundValue = $refundValue;
        $this->planValue = $planValue;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() {

        if($this->paymentPlan){
            //partial refund
            $products = $this->paymentPlan->plan->name;
            $plans = $this->paymentPlan->plan;
        }
        else{
            //total refund
            $productList = [];
            $plans = ($this->payment->type === 'R') ? [$this->payment->recurrences[0]->plan] : $this->payment->plans;
            foreach ($plans as $plan) {
                $price = formatCoin($plan->price, $plan->currency);
                $productList[] = "{$plan->name} - {$price}";
            }
            $products = join('; ', $productList);
        }

        $checkoutEmail = (is_array($plans)) ?
            $plans[0]->first()->checkout_email ?? null :
            $plans->first()->checkout_email ?? null;

        $supportText = '';
        if ($checkoutEmail) {
            $supportText = "Em caso de dúvida entre em contato com suporte: {$checkoutEmail}";
        }

        $message = $this->message;
        $message = str_replace('##NOME_ASSINANTE##', $this->subscriber->name ?? '', $message);
        $message = str_replace('##EMAIL_ASSINANTE##', $this->subscriber->email ?? '', $message);
        $message = str_replace('##NOME_PLATAFORMA##', $this->platform->name ?? '', $message);
        $message = str_replace('##TIPO_DOCUMENTO_ASSINANTE##', strtoupper($this->subscriber->document_type) ?? '', $message);
        $message = str_replace('##NUMERO_DOCUMENTO_ASSINANTE##', $this->subscriber->document_number ?? '', $message);
        $message = str_replace('##CELULAR_ASSINANTE##', $this->subscriber->cel_phone ?? '', $message);
        $message = str_replace('##VALOR_ESTORNO##', formatCoin($this->refundValue), $message);
        $message = str_replace('##VALOR_PRODUTO##', formatCoin($this->refundValue), $message);
        $message = str_replace('##CODIGO_ESTORNO##', $this->refundCode, $message);
        $message = str_replace('##PRODUTOS##', $products ?? '', $message);
        $message = str_replace('##MENSAGEM_SUPORTE##', $supportText, $message);
        $message = str_replace('##DATA##', date('d/m/Y'), $message);

        $subject =  'Comprovante de estorno';

        return $this->sendMail($subject, $message);
    }

}
