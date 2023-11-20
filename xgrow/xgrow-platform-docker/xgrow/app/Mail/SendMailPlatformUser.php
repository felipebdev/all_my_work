<?php

namespace App\Mail;

use App\Platform;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class SendMailPlatformUser extends Mailable
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
        $platform = Platform::find($this->accessData['platform_id']);

        $data = [
            'name' => $this->accessData['name'],
            'email' => $this->accessData['email'],
            'password' => $this->accessData['password'],
            'platform_name' => $platform->name
        ];

        return $this->from(env('MAIL_FROM_ADDRESS', 'naoresponda@xgrow.com'), "Dados de acesso a plataforma {$platform->name}")
            ->subject("Dados de acesso a plataforma {$platform->name}")
            ->view('emails.data-access-platform-user', compact('data'));
    }
}
