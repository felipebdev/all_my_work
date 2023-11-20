<?php

namespace App\Mail\BoletoPix;

use App\Recurrence;
use App\Utils\XorIntObfuscator;
use Carbon\CarbonImmutable;
use Illuminate\Mail\Mailable;


class SendMailUpcomingBoletoPix extends Mailable
{
    private Recurrence $recurrence;

    private $message = <<< EOT
Olá, ##NOME_ASSINANTE##
A sua assinatura do produto ##PRODUTOS##, vencerá dia ##VENCIMENTO##.
<br /><br />
Para continuar acessando, realize o pagamento no link abaixo:
<br /><br />
##LINK_PAGAMENTO##
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
        $product = $plan->product;

        $lastPayment = $this->recurrence->last_payment;
        $paymentDate = (new CarbonImmutable($lastPayment))->addDays($this->recurrence->recurrence);

        $code = XorIntObfuscator::obfuscate($this->recurrence->id);
        $url = config('app.renewal_link');
        $link = "{$url}/{$code}"; // @todo verify link

        $message = $this->message;
        $message = str_replace('##NOME_ASSINANTE##', $subscriber->name ?? '', $message);
        $message = str_replace('##PRODUTOS##', $plan->name ?? '', $message);
        $message = str_replace('##VENCIMENTO##', dateBr($paymentDate) ?? '', $message);
        $message = str_replace('##LINK_PAGAMENTO##', $link, $message);
        $message = str_replace('##EMAIL_SUPORTE##', $product->support_email, $message);

        $subject = 'Aviso de boleto/PIX';
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
