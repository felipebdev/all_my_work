<?php
namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Password;

class subscriberResetPasswordNotification extends ResetPassword
{
    private $platform;

    public function __construct($token){
        $this->token = $token;
    }

    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $this->token);
        }
        
        //url('subscribers/password/reset/' . $token)

        $url_site = sprintf('%s/index.html?token=%s&email=%s',
                            request()->url_site,
                            $this->token,
                            request()->email
                        );

        return (new MailMessage)
            ->view('subscribers.auth.emails.password', [
                'token' => $this->token,
                'user_type' => request()->user_type,
                'email' => request()->email,
                'url_site' => $url_site
            ])
            ->subject('Notificação de redefinição de senha');
    }

}
