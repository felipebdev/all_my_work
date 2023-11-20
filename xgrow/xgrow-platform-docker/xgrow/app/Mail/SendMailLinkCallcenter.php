<?php

namespace App\Mail;

use App\Email;
use App\Attendant;
use App\Mail\BaseMail;
use Illuminate\Support\Facades\Log;

class SendMailLinkCallcenter extends BaseMail
{
    private $attendant;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Attendant $attendant) {
        parent::__construct($attendant->platform_id, [$attendant->email], Email::CONSTANT_EMAIL_LINK_CALLCENTER);
        $this->attendant = $attendant;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() {
        $message = $this->template->message;
        $message = str_replace('##NOME_ATENDENTE##', $this->attendant->name ?? '', $message);
        $message = str_replace('##EMAIL_ATENDENTE##', $this->attendant->email ?? '', $message);
        $message = str_replace('##LINK_CALLCENTER##', getenv('URL_CALLCENTER') ?? '', $message);

        return $this->sendMail($this->template->subject, $message);
    }

}
