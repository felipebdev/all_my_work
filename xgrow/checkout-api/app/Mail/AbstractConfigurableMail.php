<?php

namespace App\Mail;

use App\EmailConfig;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;

abstract class AbstractConfigurableMail extends Mailable
{
    use Queueable, SerializesModels;

    public $headerName = null;

    /**
     * Set app.name, used in header
     *
     * @param  string  $name
     */
    public function setHeaderName(string $name)
    {
        $this->headerName = $name;
        Config::set('app.name', $this->headerName);
    }

    /**
     * Set reply-to if a valid mail is given
     *
     * @param  string|null  $replyToMail reply-to address
     * @param  string|null  $replyToName reply-to name, uses reply-to address if empty
     * @return bool true if successful set, false otherwise
     */
    protected function prepareReplyTo(?string $replyToMail, ?string $replyToName): bool
    {
        if (!filter_var($replyToMail, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $name = strlen($replyToName) > 0 ? $replyToName : $replyToMail;

        $this->replyTo($replyToMail, $name);

        return false;
    }

    /**
     * Load email configuration for platform from database
     *
     * @param  string  $platformId
     */
    protected function loadEmailConfig(string $platformId): void
    {
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
            $this->from($emailConfig->from_address, $emailConfig->from_name);

            $config = [
                'driver' => config('mail.driver'),
                'host' => $emailConfig->server_name,
                'port' => $emailConfig->server_port,
                'encryption' => config('mail.encryption'),
                'username' => $emailConfig->server_user,
                'password' => base64_decode($emailConfig->server_password),
                'sendmail' => config('mail.sendmail'),
                'pretend' => false,
            ];
            Config::set('mail', $config);
        }

        Config::set('mail.tag', $platformId);
    }

}
