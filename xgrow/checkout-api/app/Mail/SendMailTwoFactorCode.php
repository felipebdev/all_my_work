<?php

namespace App\Mail;

use App\Recurrence;
use App\Utils\XorIntObfuscator;
use Carbon\CarbonImmutable;
use Illuminate\Mail\Mailable;


class SendMailTwoFactorCode extends Mailable
{
    private $data;
    private $pin;

    private $message = <<< EOT
Olá, ##NOME_ASSINANTE##
O código de segurança para a confirmação do reembolso é ##PIN##
EOT;

    public function __construct($data, $pin)
    {
        $this->data = $data;
        $this->pin = $pin;
    }

    public function build()
    {
        $pin = $this->pin;
        $data = $this->data;
        $message = $this->message;
        $message = str_replace('##NOME_ASSINANTE##', $data['name'] ?? '', $message);
        $message = str_replace('##PIN##', $pin ?? '', $message);

        $subject = 'Código de segurança';
        return $this->from('renovacao@xgrow.com')
            ->to($data['email'], $data['name'])
            ->subject($subject)
            ->markdown('emails.auto')
            ->with([
                'SUBJECT' => $subject,
                'PREVIEW' => $subject,
                'message' => $message,
            ]);
    }
}
