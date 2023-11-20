<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendEmailUserRegistering extends Mailable
{
    use Queueable, SerializesModels;

    protected $accessData;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($accessData)
    {
        $this->accessData = $accessData;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $data = [
            'name' => $this->accessData['name'],
            'email' => $this->accessData['email'],
            'password' => $this->accessData['password'],
        ];

        return $this->from(env('MAIL_FROM_ADDRESS', 'naoresponda@xgrow.com'), "Bem-vindo a Xgrow")
            ->subject("Bem-vindo a Xgrow")
            ->view('emails.register-user', compact('data'));
    }
}
