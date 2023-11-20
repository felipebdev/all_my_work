<?php

namespace App\Mail\BoletoPix;

use App\Recurrence;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendMailCancelSubscriptionBoletoPix extends Mailable
{
    use Queueable;
    use SerializesModels;

    private Recurrence $recurrence;

    private $message = <<< EOT
Olá, ##NOME_ASSINANTE##
A sua assinatura do(s) produto(s) ##PRODUTOS## foi cancelada.
<br /><br />
Caso tenha alguma dificuldade estamos prontos para te atender nesse contato ##EMAIL_SUPORTE##
EOT;

    public function __construct(Recurrence $recurrence)
    {
        $this->recurrence = $recurrence;
    }

    public function build()
    {
        $subscriber = $this->recurrence->subscriber;
        $plan = $this->recurrence->plan;
        $product = isset($plan->product)?? $plan->product;
        $support_email = isset($product->support_email)? $product->support_emai : 'Email do produtor';

        $message = $this->message;
        $message = str_replace('##NOME_ASSINANTE##', $subscriber->name ?? '', $message);
        $message = str_replace('##PRODUTOS##', $plan->name ?? '', $message);
        $message = str_replace('##EMAIL_SUPORTE##', $support_email, $message);

        $subject = 'Aviso de cancelamento de assinatura';
        return $this->from('renovacao@xgrow.com')
            ->to($subscriber->email, $subscriber->name)
            ->subject($subject)
            ->markdown('emails.auto')
            ->with([
                'SUBJECT' => $subject,
                'PREVIEW' => $subject,
                'message' => $message,
            ]);
    }
}
