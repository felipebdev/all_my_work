<?php

namespace App\Mail;

use App\Email;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Platform;
use App\EmailPlatform;
use Illuminate\Support\Facades\Log;

abstract class BaseMail extends AbstractConfigurableMail
{
    public $mailId;
    public $recipients;
    public $platform;
    public $mainTemplate; // Template from admin
    public $template;

    abstract public function build();

    public function __construct($platformId, $recipients, $mailId = 1) {
        $this->recipients = $recipients;
        $this->mailId = $mailId;
        $this->baseBuild($platformId);
    }

    private function baseBuild($platformId) {
        $this->platform = Platform::find($platformId);
        $this->prepareReplyTo($this->platform->reply_to_email, $this->platform->reply_to_name);
        $this->loadEmailConfig($platformId);
        $this->setHeaderName($this->platform->name);
        $this->setTemplate($this->mailId, $platformId);
        config(['mail.tag' => $platformId]);
    }

    protected function sendMail($subject, $message, $recipients = [], $withData = []) {

        if (empty($subject) || trim($subject) == '') {
            $subject = $this->platform->name.' - '.$this->mainTemplate->subject;
        }

        if (empty($recipients)) {
            $recipients = $this->recipients;
        }

        return $this
            ->to($recipients)
            ->subject($subject)
            ->markdown('emails.auto')
            ->with(array_merge($withData, [
                'message' => $message,
                'subject' => $subject
            ])
            );
    }

    /**
     * Set email template
     *
     * @param $emailId
     * @param  null  $platformId
     * @return \App\Mail\BaseMail
     */
    protected function setTemplate($emailId, $platformId = null)
    {
        try {
            // Get main template
            $this->mainTemplate = Email::where('id', $emailId)->firstOrFail();

            // Use personalized template, main template if not personalized
            $this->template = EmailPlatform::where('platform_id', $platformId)->where('email_id', $emailId)->first()
                ?? $this->mainTemplate;

        } catch(ModelNotFoundException $e) {
            Log::error('Email template not found', [
                'email_id' => $emailId,
                'reason' => 'Email not set on Admin'
            ]);

            return $this->sendMail(
                'Padrão de e-mail não encontrado',
                "E-mail não cadastrado, favor entrar em contato com o suporte. (ID {$emailId})",
                [$this->replyTo['address']]
            );
        }
    }
}
