<?php

namespace App\Mail;

use Auth;
use Config;
use App\EmailConfig;
use App\Platform;
use App\PlatformUser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendMailTest extends Mailable
{
    use Queueable, SerializesModels;

    public $emailData;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($emailData)
    {
        $this->emailData = $emailData;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $user = Auth::user() ?? auth('api')->user();

        $emailConfig = EmailConfig::where('platform_id', $user->platform_id)->first();

        if ($emailConfig && $emailConfig->valid_email === 1)
        {
            $config = array(
                'driver'     => config('mail.driver'),
                'host'       => $emailConfig->server_name,
                'port'       => $emailConfig->server_port,
                'from'       => array('address' => $emailConfig->from_address, 'name' => $emailConfig->from_name),
                'encryption' => config('mail.encryption'),
                'username'   => $emailConfig->server_user,
                'password'   => base64_decode($emailConfig->server_password),
                'sendmail'   => config('mail.sendmail'),
                'pretend'    => false,
            );
            Config::set('mail', $config);
        }

        return $this->from($emailConfig->from_address, $emailConfig->from_name)
            ->subject($this->emailData['subject'])
            ->markdown('emails.test')
            ->with([
                'emailData' => $this->emailData
            ]);
    }
}

