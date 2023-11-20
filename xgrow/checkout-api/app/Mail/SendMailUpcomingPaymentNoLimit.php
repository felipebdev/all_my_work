<?php

namespace App\Mail;

use App\Payment;
use Carbon\CarbonImmutable;
use Illuminate\Mail\Mailable;

class SendMailUpcomingPaymentNoLimit extends Mailable
{
    private $payment;

    private $message = <<< EOT
Olá, ##NOME_ASSINANTE##,<br />
O pagamento do produto ##PRODUTOS## será efetuado no dia ##DATA_COBRANCA##.
<br /><br />
Atenção: se seu pagamento é feito com cartão de crédito, seu acesso será renovado automaticamente.
<br /><br />
Caso tenha alguma dúvida, entre em contato com nossa Equipe de Suporte pelo site https://suporte.xgrow.com.
<br /><br />
Atenciosamente,<br />
Equipe Xgrow.com
EOT;

    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }

    public function build()
    {
        $subscriber = $this->payment->subscriber;

        $plans = ($this->payment->type === Payment::TYPE_SUBSCRIPTION)
            ? collect([$this->payment->recurrences[0]->plan])
            : $this->payment->plans;

        $products = $plans->implode('name', ', ');

        $paymentDate = new CarbonImmutable($this->payment->payment_date);

        $message = $this->message;
        $message = str_replace('##NOME_ASSINANTE##', $subscriber->name ?? '', $message);
        $message = str_replace('##PRODUTOS##', $products ?? '', $message);
        $message = str_replace('##DATA_COBRANCA##', dateBr($paymentDate) ?? '', $message);

        return $this->from('renovacao@xgrow.com')
            ->to($subscriber->email, $subscriber->name)
            ->subject('Aviso de pagamento')
            ->markdown('emails.auto')
            ->with([
                'message' => $message,
                'SUBJECT' => 'Aviso de pagamento'
            ]);
    }
}
