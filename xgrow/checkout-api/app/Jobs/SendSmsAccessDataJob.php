<?php

namespace App\Jobs;

use App\Facades\MagicToken;
use App\Services\BulkGate\BukGateSender;
use App\Subscriber;
use App\Subscription;
use BulkGate\Sdk\ApiException;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class SendSmsAccessDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $time;
    public string $platformId;
    public Subscriber $subscriber;
    public string $bitlyToken;
    public bool $dryRun;
    public ?string $password = '';

    public function __construct(string $time, string $platformId, Subscriber $subscriber, $bitlyToken, $dryRun, $password)
    {
        $this->onQueue('xgrow-jobs:sms');

        $this->time = $time;
        $this->platformId = $platformId;
        $this->subscriber = $subscriber;
        $this->bitlyToken = $bitlyToken;
        $this->dryRun = $dryRun;
        $this->password = $password;
    }

    public function handle(BukGateSender $sender)
    {
        $platformId = $this->platformId;
        $subscriber = $this->subscriber;

        $email = $subscriber->email;
        $celPhone = $subscriber->cel_phone;
        $phoneNumber = '55'.preg_replace('/[^0-9]/', '', $celPhone);

        $subscriptions = $this->getSubscriptions($subscriber->id);

        $firstSubscription = $subscriptions->first();

        if (!$firstSubscription) {
            return;
        }

        $plan = $firstSubscription->plan;

        $productName = $plan->product->name;
        $contact = $plan->product->support_email;
        $message = "Dados Acesso {$productName}. Acesse o link: {$subscriber->platform->url} informando o e-mail: {$subscriber->email} e senha: {$this->password}. Se precisar fale conosco: {$contact}";

        $status = 'success';

        try {
            if (!$this->dryRun) {
                $response = $sender->sendMessage($phoneNumber, $message);
                $status = $response->status ?? 'success';
            }
        } catch (ApiException $e) {
            $status = $e->getMessage();
        }

        $this->writeCsvDebugFile($email, $phoneNumber, $message, $status);
    }

    private function getSubscriptions(string $subscriberId)
    {
        return Subscription::query()
            ->where('subscriber_id', $subscriberId)
            ->whereNull('payment_pendent')
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('canceled_at')
                    ->orWhere('canceled_at', '>', Carbon::now());
            })->get();
    }

    private function getMagicLink($platformId, $email, $subscriberId, $productName): string
    {
        $magicToken = MagicToken::generate($platformId, $subscriberId);
        $url = "https://la.xgrow.com/{$platformId}?magic={$magicToken}";

        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$this->bitlyToken,
            'Content-Type' => 'application/json'
        ])->post('https://bityli.com/api/url/add', [
            'url' => $url,
            'domain' => "https://go.xgrow.com",
            'metatitle' => "Acesso a plataforma",
            'metadescription' => "{$productName}, utilize esse link para acessar a plataforma."
        ]);

        return $response['shorturl'];
    }

    private function writeCsvDebugFile($email, $phoneNumber, $message, $status): void
    {
        $info = [
            $email,
            $phoneNumber,
            str_replace("\n", '', nl2br($message)),
            $status,
        ];

        $filename = "sms-access-log-{$this->time}.csv";

        Storage::disk('local')->append($filename, join(';', $info));
    }

}
