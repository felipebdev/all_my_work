<?php

namespace App\Mail;

use App\Email;
use App\Payment;
use App\Subscriber;

use function route;

class SendMailBankSlip extends BaseMail
{
    private $subscriber;
    private $payment;

    public function __construct($platformId, Subscriber $subscriber, Payment $payment)
    {
        parent::__construct($platformId, [$subscriber->email], Email::CONSTANT_EMAIL_BOLETO);
        $this->subscriber = $subscriber;
        $this->payment = $payment;
    }

    public function build()
    {
        $plans = ($this->payment->type === 'R')
            ? collect([$this->payment->recurrences[0]->plan])
            : $this->payment->plans;

        $pricePaidByCustomer = formatCoin($this->payment->price);

        $subject = 'Seu pagamento está pendente';//$this->template->subject;

        $mainPlan = $plans->first();
        $product = $mainPlan->product;
        $checkoutEmail = $product->support_email ?? $mainPlan->checkout_email ?? null;

        $supportText = '';
        if ($checkoutEmail) {
            $supportText = "Para dúvidas relacionada ao produto, <br>entre em contato com o produtor em: <strong>{$checkoutEmail}</strong>";
        }

        return $this
            ->to($this->recipients)
            ->subject($subject)
            ->view('emails.boleto')
            ->with([
                'SUBJECT' => $subject,
                'PREVIEW' => 'Boleto de cobrança',
                'CODIGO_COMPRA' => $this->payment->order_code ?? '',
                'NOME_ASSINANTE' => $this->subscriber->name ?? '',
                'NOMECURSO' => $plans->pluck('name')->join('; '),
                'BOLETO_BARCODE' => $this->payment->boleto_line,
                'NOME_PRODUTO' => $plans->pluck('name')->join('; '),
                'HORA_COMPRA' => $this->payment->created_at->format('d/m/Y H:i:s'),
                'QUANTIDADE_COMPRA' => 1, // ?
                'VALOR_COMPRA' => formatCoin($this->payment->plans_value), // verificar esse valor
                'VALOR_PAGO' => $pricePaidByCustomer,
                'BOLETO_URL' => $this->getBoletoUrl(),
                'supportText' => $supportText,
                //'message' => '',
            ]);
    }

    private function getBoletoUrl(): string
    {
        return $this->payment->boleto_url
            ? route('checkout.boleto.download', $this->payment->order_code)
            : '';
    }

}
