<?php

namespace App\Mail;

use App\Email;
use App\Payment;
use App\Subscriber;
use Illuminate\Support\Str;

class SendMailPurchaseProof extends BaseMail
{
    private $platformId;
    private $subscriber;
    private $payment;
    private $password;

    public static $VARIABLES = [
        '##NOME_ASSINANTE##' => 'Nome do Assinante',
        '##EMAIL_ASSINANTE##' => 'Email do Assinante',
        '##NOME_PLATAFORMA##' => 'Nome da Plataforma',
        '##LINK_PLATAFORMA##' => 'Link de acesso à Plataforma',
        '##AUTO##' => 'Senha de acesso gerada automaticamente',
        '##TIPO_DOCUMENTO_ASSINANTE##' => 'Tipo de documento do Assinante',
        '##NUMERO_DOCUMENTO_ASSINANTE##' => 'Número de documento do Assinante',
        '##CELULAR_ASSINANTE##' => 'Celular do Assinante',
        '##PRODUTOS##' => 'Produtos adquiridos',
        '##VALOR_PAGO##' => 'Valor pago pelo Assinante',
        '##PARCELAS##' => 'Quantidade de parcelas (se existir)',
        '##CODIGO_COMPRA##' => 'Código de compra',
        '##MENSAGEM_SUPORTE##' => 'Mensagem de contato com suporte',
    ];

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($platformId, Subscriber $subscriber, Payment $payment, $password = null) {
        parent::__construct($platformId, [$subscriber->email], Email::CONSTANT_EMAIL_PURCHASE_PROOF);
        $this->subscriber = $subscriber;
        $this->payment = $payment;
        $this->password = $password;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() {
        $products = "";
        $paymentType = $this->payment->type;

        $plans = ($paymentType === Payment::TYPE_SUBSCRIPTION)
            ? collect([$this->payment->recurrences[0]->plan])
            : $this->payment->plans;

        $mainPlan = $plans->first();

        $product = $mainPlan->product;
        $this->setHeaderName($product->name);

        foreach ($plans as $plan) {
            $price = formatCoin($plan->price, $plan->currency);
            $products .= "{$plan->name} - {$price}<br>";
        }

        $subject = "Bem-vindo para {$product->name} - Dados de Acesso";

        $message = ($mainPlan->product->message_email ?? null)
            ? nl2br($mainPlan->product->message_email)
            : $this->template->message;

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

        $checkoutEmail = $product->support_email ?? $mainPlan->checkout_email ?? null;

        $supportText = '';
        if ($checkoutEmail) {
            $supportText = "Para dúvidas relacionada ao produto, entre em contato com o produtor em: {$checkoutEmail}";
        }

        $message = str_replace('##NOME_ASSINANTE##', $this->subscriber->name ?? '', $message);
        $message = str_replace('##EMAIL_ASSINANTE##', $this->subscriber->email ?? '', $message);
        $message = str_replace('##NOME_PLATAFORMA##', $this->platform->name ?? '', $message);
        $message = str_replace('##LINK_PLATAFORMA##', $this->platform->url ?? '', $message);
        $message = str_replace('##AUTO##', $this->password ?? '', $message);
        $message = str_replace('##TIPO_DOCUMENTO_ASSINANTE##', strtoupper($this->subscriber->document_type) ?? '', $message);
        $message = str_replace('##NUMERO_DOCUMENTO_ASSINANTE##', $this->subscriber->document_number ?? '', $message);
        $message = str_replace('##CELULAR_ASSINANTE##', $this->subscriber->cel_phone ?? '', $message);
        $message = str_replace('##PRODUTOS##', $products ?? '', $message);
        $message = str_replace('##VALOR_PAGO##', $pricePaidByCustomer, $message);
        $message = str_replace('##PARCELAS##', $installmentText, $message);
        $message = str_replace('##CODIGO_COMPRA##', $this->payment->order_code ?? '', $message);

        if (Str::contains($message, '##MENSAGEM_SUPORTE##')) {
            $message = str_replace('##MENSAGEM_SUPORTE##', $supportText, $message);
        } else {
            // force support message
            $message .= '<br /><br />'.$supportText.'<br /><br />';
        }

        return $this->sendMail($subject, $message);
    }

}
