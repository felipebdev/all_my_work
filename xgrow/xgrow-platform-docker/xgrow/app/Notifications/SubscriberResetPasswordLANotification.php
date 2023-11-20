<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriberResetPasswordLANotification extends Notification
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
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->view('emails.password-recovery-la', [
                'token' => $this->recoveryData['token'],
                'name' => $this->recoveryData['name'],
                'url' => $this->recoveryData['url'],
                'email' => $this->recoveryData['email'],
                'platformName' => $this->recoveryData['platformName'],
            ])
            ->subject('Recuperação de senha');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
