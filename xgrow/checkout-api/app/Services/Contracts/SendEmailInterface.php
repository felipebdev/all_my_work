<?php

namespace App\Services\Contracts;

interface SendEmailInterface
{
    /**
     * Send email to a single recipient
     *
     * @param string $subject
     * @param string $text
     * @param string $recipient
     * @param string $reply_to
     * @return array
     */
    public function sendSingleEmail(string $subject, string $text, string $recipient, string $reply_to);

    public function sendEmailToRecipients(string $subject, string $text, array $recipients, ?string $replyTo);

}
