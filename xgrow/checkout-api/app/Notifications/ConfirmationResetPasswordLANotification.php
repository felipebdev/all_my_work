<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ConfirmationResetPasswordLANotification extends Notification
{
    use Queueable;

    private $recoveryData;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($recoveryData)
    {
        $this->recoveryData = $recoveryData;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->view('emails.password-changed-la', [
                'name' => $this->recoveryData['name'],
                'url' => $this->recoveryData['url_site'],
                'platformName' => $this->recoveryData['platformName'],
            ])
            ->subject('Notificação de redefinição de senha');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
