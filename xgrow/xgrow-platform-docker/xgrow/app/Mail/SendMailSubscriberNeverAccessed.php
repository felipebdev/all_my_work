<?php

namespace App\Mail;

use App\Email;
use App\Platform;
use Illuminate\Support\Facades\DB;

class SendMailSubscriberNeverAccessed extends BaseMail
{
    private $subscriber;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($platformId, $subscriber)
    {
        parent::__construct($platformId, [$subscriber->email], Email::CONSTANT_EMAIL_ACCESS_DATA);
        $this->subscriber = $subscriber;
    }

    /**
     * Build the message.
     *
     * @return BaseMail
     */
    public function build()
    {
        $platform = Platform::select(['url'])->find($this->subscriber->platform_id);

        $password = keygen(12);

        $this->subscriber->update([
            'raw_password' => $password
        ]);

        $subject = 'Dados de Acesso';
        $supportText = "Para dúvidas relacionada ao produto, <br>entre em contato com o produtor.";
        $subscriptions = $this->subscriber->subscriptions->first();

        if(!empty($subscriptions)){
            $product = $subscriptions->plan->product;
            $subject = 'Dados de Acesso - '.$product->name;
            $supportText = "Para dúvidas relacionada ao produto, <br>entre em contato com o produtor em: <strong>{$product->support_email}</strong>";
        }

        $message = $this->template->message;
        $message = str_replace('##NOME_ASSINANTE##', $this->subscriber->name, $message);
        $message = str_replace('##EMAIL_ASSINANTE##', $this->subscriber->email, $message);
        $message = str_replace('##AUTO##', $password, $message);
        $message = str_replace('##LINK_PLATAFORMA##', $platform->url, $message);

        return $this->sendMail($subject, $message, [], ['supportText' => $supportText]);
    }
}
