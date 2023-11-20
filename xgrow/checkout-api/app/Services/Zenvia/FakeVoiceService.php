<?php

namespace App\Services\Zenvia;

use App\Services\Contracts\RejectableInterface;
use App\Services\Contracts\SendVoiceInterface;
use App\Services\Objects\PhoneResponse;
use Illuminate\Support\Facades\Log;

class FakeVoiceService implements SendVoiceInterface, RejectableInterface
{
    private $rejected = [];

    public function sendVoiceToNumbers(string $publicAudioUrl, array $numbers): array
    {
        $batchId = uniqid();
        $responses = [];
        $this->rejected = [];

        $valid = $this->filterNumbers($numbers);

        foreach ($valid as $index => $number) {
            $fakeResponseId = uniqid();
            Log::debug(
                'FakeSmsService',
                [
                    'batch' => $batchId,
                    'uniqid' => $fakeResponseId,
                    'audio_url' => $publicAudioUrl,
                    'index' => $index,
                    'number' => $number
                ]
            );
            $responses[] = new PhoneResponse(true, 200, 'enviado com sucesso', $number, $fakeResponseId);
        }
        return $responses;
    }

    public function getRejected(): array
    {
        return $this->rejected;
    }

    private function filterNumbers(array $numbers)
    {
        return collect($numbers)->filter(function ($number) {
            $stripped =  preg_replace('/[^0-9]/', '', $number);
            $looksMobilePhone = strlen($stripped) == 11; // Brazilian numbers only
            if (!$looksMobilePhone) {
                $this->rejected[] = $number;
            }
            return $looksMobilePhone;
        })->toArray();
    }

}
