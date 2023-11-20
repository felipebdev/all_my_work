<?php

namespace App\Mail;

use App\Email;
use App\Subscriber;

class SendLinkOffer extends BaseMail
{

    private $platformId;
    private $subscriber;
    private $url;

    public function __construct($platformId, Subscriber $subscriber, string $url)
    {
        parent::__construct($platformId, [$subscriber->email], Email::CONSTANT_EMAIL_LINK_OFFER);
        $this->platformId = $platformId;
        $this->subscriber = $subscriber;
        $this->url = $url;
    }

    /**
     * Build the message.
     *
     * @return $this
     * @throws \Exception
     */
    public function build()
    {
        $message = $this->template->message;
        $message = str_replace('##NOME_ASSINANTE##', $this->subscriber->name, $message);
        $message = str_replace('##LINK_OFFER##', $this->getLink(), $message);

        return $this->sendMail($this->template->subject, $message);
    }

    private function getLink()
    {
        return "<a href='{$this->url}' target='_blank'>Clique aqui</a>";
    }

}
