<?php

namespace App\Mail;

use App\Recurrence;
use Carbon\CarbonImmutable;
use Illuminate\Mail\Mailable;

class SendMailUpcomingRecurrenceSubscription extends Mailable
{
    private $recurrence;

    private $message = <<< EOT
Olá, ##NOME_ASSINANTE##,<br />
A assinatura do produto ##PRODUTOS## será renovada no dia ##DATA_COBRANCA##.
<br /><br />
Atenção: se seu pagamento é feito com cartão de crédito, sua assinatura será renovada automaticamente.
Se é feito com boleto, você receberá em seu email um novo boleto para pagamento na data da próxima cobrança.
<br /><br />
Caso tenha alguma dúvida, entre em contato com nossa Equipe de Suporte pelo site https://suporte.xgrow.com.
<br /><br />
Atenciosamente,<br />
Equipe Xgrow.com
EOT;

    public function __construct(Recurrence $recurrence)
    {
        $this->recurrence = $recurrence;
    }

    public function build()
    {
        $subscriber = $this->recurrence->subscriber;

        $plan = $this->recurrence->plan;

        $products = $plan->name;

        $paymentDate = new CarbonImmutable($this->recurrence->last_payment);
        $upcomingDate = $paymentDate->addDays($this->recurrence->recurrence);

        $message = $this->message;
        $message = str_replace('##NOME_ASSINANTE##', $subscriber->name ?? '', $message);
        $message = str_replace('##PRODUTOS##', $products ?? '', $message);
        $message = str_replace('##DATA_COBRANCA##', dateBr($upcomingDate) ?? '', $message);

        return $this->from('renovacao@xgrow.com')
            ->to($subscriber->email, $subscriber->name)
            ->subject('Aviso de renovação de assinatura')
            ->markdown('emails.auto')
            ->with(['message' => $message]);
    }
}
