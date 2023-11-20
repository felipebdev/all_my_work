<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriberNotAccess extends Notification
{
    use Queueable;

    private $subscriber;
    private $plan;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($subscriber, $plan)
    {
        $this->subscriber = $subscriber;
        $this->plan = $plan;
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
        $url = route('confirm.access.email');
        return (new MailMessage)->view(
            'emails.subscriber-not-access-course',
            [
                'name' => $this->subscriber->name,
                'email' => $this->subscriber->email,
                'url' => $url,
                'plan' => $this->plan->planName,
            ]
        )->subject('Olá, Precisa de ajuda?');
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
