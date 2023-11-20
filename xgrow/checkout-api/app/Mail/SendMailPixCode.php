<?php

namespace App\Mail;

use App\Email;
use App\Payment;
use App\Subscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;

class SendMailPixCode extends BaseMail
{
    use Queueable, SerializesModels;

    public function __construct($platformId, Subscriber $subscriber, Payment $payment)
    {
        parent::__construct($platformId, [$subscriber->email], Email::CONSTANT_EMAIL_PIX);
        $this->subscriber = $subscriber;
        $this->payment = $payment;
    }

    public function build()
    {
        $message = $this->template->message;

        $message = str_replace('##NOME_ASSINANTE##', $this->subscriber->name ?? '', $message);
        $message = str_replace('##EMAIL_ASSINANTE##', $this->subscriber->email ?? '', $message);
        $message = str_replace('##NOME_PLATAFORMA##', $this->platform->name ?? '', $message);
        //$message = str_replace('##NOME_PLATAFORMA##', $subscriber->plan ?? '', $message);
        $message = str_replace('##PIX_QRCODE##', $this->payment->pix_qrcode ?? '', $message);
        $message = str_replace('##PIX_URL##', $this->payment->pix_qrcode_url ?? '', $message);

        return $this->sendMail($this->template->subject, $message);
    }
}
