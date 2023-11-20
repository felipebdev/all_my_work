<?php

namespace App\Mail;

use App\Email;
use App\Payment;
use App\Subscriber;
use App\Mail\BaseMail;

class SendMailAccessData extends BaseMail
{
    private $subscriber;
    private $password;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Subscriber $subscriber, $password) {
        $this->subscriber = $subscriber;
        $this->password = $password;
        parent::__construct($subscriber->platform_id, [$subscriber->email], Email::CONSTANT_EMAIL_NEW_REGISTER);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() {
        $message = $this->template->message;
        $message = str_replace('##NOME_ASSINANTE##', $this->subscriber->name ?? '', $message);
        $message = str_replace('##EMAIL_ASSINANTE##', $this->subscriber->email ?? '', $message);
        $message = str_replace('##NOME_PLATAFORMA##', $this->platform->name ?? '', $message);
        $message = str_replace('##LINK_PLATAFORMA##', $this->platform->url ?? '', $message);
        $message = str_replace('##AUTO##', $this->password ?? '', $message);

        return $this->sendMail($this->template->subject, $message);
    }

}
