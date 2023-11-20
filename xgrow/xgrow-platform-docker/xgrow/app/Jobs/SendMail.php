<?php

namespace App\Jobs;

use App\Mail\BaseMail;
use App\Mail\SendMailPurchaseProof;
use App\Services\EmailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $mail;
    public $recipient;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(BaseMail $mail, array $recipient)
    {
        $this->mail = $mail;
        $this->recipient = $recipient;

        self::onQueue('xgrow-emails');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        EmailService::mail($this->recipient, $this->mail);
    }
}
