<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BaseTemplate extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * "The preview text is a short message that displays below the subject line in many email clients"
     * Most email clients displays only 40-140 characters, so keep it concise.
     *
     * @var string
     */
    public $previewText = '';

    public function __construct(string $previewText = '')
    {
        if ($previewText) {
            $strlen = strlen($previewText);
            $this->previewText = $previewText . str_repeat('&nbsp;&zwnj;', 200 - $strlen);
        }
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.welcome');
    }
}
