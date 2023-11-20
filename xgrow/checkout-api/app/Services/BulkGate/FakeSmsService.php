<?php

namespace App\Services\BulkGate;

use App\Services\Contracts\RejectableInterface;
use App\Services\Contracts\SendSmsInterface;
use App\Services\Objects\PhoneResponse;
use Illuminate\Support\Facades\Log;

class FakeSmsService implements SendSmsInterface, RejectableInterface
{
    private $rejected = [];

    public function sendSmsToNumbers(string $text, array $numbers): array
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
                    'text' => $text,
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
            $looksMobilePhone = strlen($stripped) >= 11;
            if (!$looksMobilePhone) {
                $this->rejected[] = $number;
            }
            return $looksMobilePhone;
        })->toArray();
    }

}
