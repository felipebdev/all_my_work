<?php

namespace App\Mail;

use App\Platform;
use App\PlatformUser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendMailSupport extends Mailable
{
    use Queueable, SerializesModels;

    public $platformUser;
    public $emailData;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(PlatformUser $platformUser, $emailData)
    {
        $this->platformUser = $platformUser;
        $this->emailData = $emailData;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        $platform = Platform::find($this->platformUser->platform_id);

        return $this->from(config('constants.emailSupport.from'), $this->platformUser->email)
            ->subject(config('constants.emailSupport.subject') ." - " . $this->emailData['subject'])
            ->markdown('emails.support')
            ->with([
                'user' => $this->platformUser,
                'emailData' => $this->emailData
            ]);
    }
}

