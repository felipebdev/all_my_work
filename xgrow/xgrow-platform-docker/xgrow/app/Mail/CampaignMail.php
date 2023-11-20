<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CampaignMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $replyTo;
    public $text;

    public function __construct(string $subject, string $text, ?string $replyTo)
    {
        $this->subject = $subject;
        $this->replyTo = $replyTo;
        $this->text = $text;
    }

    public function build()
    {
        return $this->subject($this->subject)
            ->replyTo($this->replyTo)
            ->html($this->text);
    }
}
