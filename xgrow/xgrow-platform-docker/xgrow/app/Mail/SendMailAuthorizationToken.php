<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Auth;

class SendMailAuthorizationToken extends Mailable
{
    protected $token;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $token = $this->token;
        $name = Auth::user()->name;
        $subject = "Código de Autenticação Xgrow.";
        return $this->from(env('MAIL_FROM_ADDRESS', 'naoresponda@xgrow.com'),
            "Código de Autenticação Xgrow.")
            ->subject("Código de Autenticação Xgrow")
            ->view('emails.authorization-token', compact('token', 'name', 'subject'));
    }
}
