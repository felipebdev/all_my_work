<?php

namespace App\Mail;

use App\Email;
use App\Payment;
use App\PaymentPlan;
use App\Subscriber;
use App\Mail\BaseMail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SendMailRefund extends BaseMail
{
    private $subscriber;
    private $refundCode;
    private $refundValue;
    private $planValue;
    private $RefundDate;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($platformId, Subscriber $subscriber, PaymentPlan $paymentPlan, $refundCode, $refundValue, $planValue, $RefundDate) {
        parent::__construct($platformId, [$subscriber->email], Email::CONSTANT_EMAIL_REFUND);
        $this->subscriber = $subscriber;
        $this->paymentPlan = $paymentPlan;
        $this->refundCode = $refundCode;
        $this->refundValue = $refundValue;
        $this->planValue = $planValue;
        $this->RefundDate = $RefundDate;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() {

        $plans = ($this->paymentPlan->payment->type === 'R')
            ? collect([$this->paymentPlan->payment->recurrences[0]->plan])
            : $this->paymentPlan->plan;

        $plan = $plans->first() ?? null;

        if (!$plan) {
            Log::error('No plan found for recurrence on SendMailRefund', [
                'payment_plan_id' => $this->paymentPlan->id ?? null,
                'payment_type' => $this->paymentPlan->payment->type ?? null,
            ]);
        }

        $price = formatCoin($this->paymentPlan->plan_value, $plan->currency ?? 'BRL');
        $product = "{$plan->name} - {$price}<br>";

        $checkoutEmail = $plan->checkout_email ?? null;

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
        $message = str_replace('##VALOR_ESTORNO##', formatCoin($this->refundValue), $message);
        $message = str_replace('##VALOR_PRODUTO##', formatCoin($this->refundValue), $message);
        $message = str_replace('##CODIGO_ESTORNO##', $this->refundCode, $message);
        $message = str_replace('##DATA_ESTORNO##', dateBr($this->RefundDate, 1), $message);
        $message = str_replace('##PRODUTOS##', $product ?? '', $message);
        $message = str_replace('##MENSAGEM_SUPORTE##', $supportText, $message);

        return $this->sendMail($this->template->subject, $message);
    }

}
