<?php

namespace App\Services\CampaignEmail;

use App\EmailConfig;
use App\Jobs\CampaignEmailRateLimitedQueue;
use App\Mail\CampaignMail;
use App\Services\Contracts\SendEmailInterface;
use App\Services\EmailTaggedService;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

class SendEmailService implements SendEmailInterface
{

    private $platformId;

    public function __construct(string $platformId)
    {
        $this->platformId = $platformId;

        $emailConfig = EmailConfig::where('platform_id', $platformId)->first();

        if ($emailConfig !== null &&
            isset($emailConfig) &&
            $emailConfig->valid_email === 1 &&
            $emailConfig->from_name !== '' &&
            $emailConfig->from_address !== '' &&
            $emailConfig->server_name !== '' &&
            $emailConfig->server_port !== '' &&
            $emailConfig->server_user !== '' &&
            $emailConfig->server_password !== '' &&
            $emailConfig->platform_id !== ''
        ) {
            $config = array(
                'driver'     => $emailConfig->driver,
                'host'       => $emailConfig->server_name,
                'port'       => $emailConfig->server_port,
                'from'       => array('address' => $emailConfig->from_address, 'name' => $emailConfig->from_name),
                'encryption' => config('mail.encryption'),
                'username'   => $emailConfig->server_user,
                'password'   => base64_decode($emailConfig->server_password),
                'sendmail'   => config('mail.sendmail'),
                'pretend'    => false,
            );
            Config::set('mail', $config);
        }
    }

    public function sendEmailToRecipients(string $subject, string $text, array $recipients, ?string $replyTo)
    {
        $results = [];
        foreach ($recipients as $recipient) {
            CampaignEmailRateLimitedQueue::dispatch($this->platformId, $subject, $text, $recipient, $replyTo);
            $results[] = true; // queued
        }
        return $results;
    }

    public function sendSingleEmail(string $subject, string $text, string $recipient, ?string $replyTo): bool
    {
        $mail = new CampaignMail($subject, $text, $replyTo);
        EmailTaggedService::mail($this->platformId, 'CAMPAIGN', $mail, [$recipient]);

        return true;
    }


}
