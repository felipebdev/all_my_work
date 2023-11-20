<?php

namespace App\Services\Pagarme;

use App\Exceptions\Finances\ActionFailedException;
use App\Exceptions\Finances\InvalidRecipientException;
use App\Exceptions\RecipientFailedException;
use App\Services\Mundipagg\Objects\RecipientData;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use stdClass;

class PagarmeRawClient
{
    private static $baseUrl = 'https://api.pagar.me/core/v5/';

    /**
     * @param  \App\Services\Mundipagg\Objects\RecipientData  $data
     * @return \stdClass
     * @throws \App\Exceptions\RecipientFailedException
     */
    public function createRecipient(RecipientData $data): stdClass
    {
        $branchCheckDigit = strlen($data->branchCheckDigit) > 0 ? $data->branchCheckDigit : null; // empty to null

        $data = [
            'name' => Str::limit($data->name, 128, ''),
            'email' => Str::limit($data->email, 64, ''),
            'description' => Str::limit($data->description, 256, ''),
            'document' => $data->document,
            'type' => 'individual',
            // 'code' => 'code',
            'default_bank_account' => [
                'holder_name' => Str::limit($data->holderName, 30, ''),
                'bank' => $data->bank,
                'branch_number' => $data->branchNumber,
                'branch_check_digit' => $branchCheckDigit,
                'account_number' => $data->accountNumber,
                'account_check_digit' => $data->accountCheckDigit,
                'holder_type' => 'individual',
                'holder_document' => $data->document,
                'type' => $data->accountType ?? null,
            ],
            'automatic_anticipation_settings' => [
                'enabled' => true,
                'type' => '1025',
                'volume_percentage' => '100',
                'days' => [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,30,31],
                'delay' => '29', //se for D+30 o delay fica em 29.
            ],
        ];

        $result = Http::withBasicAuth($this->secret(), '')->post(self::$baseUrl.'/recipients', $data);

        if (!$result->successful()) {
            throw new RecipientFailedException($result->json()['message'] ?? 'Erro desconhecido');
        }

        $json = $result->json();

        Log::debug('PagarmeRawClient::createRecipient json', [
            'result' => $json,
        ]);

        return (object) $json;
    }

    /**
     * @param  string  $recipientId
     * @return array
     * @throws \App\Exceptions\Finances\ActionFailedException
     * @throws \App\Exceptions\Finances\InvalidRecipientException
     */
    public function obtainRecipient(string $recipientId): array
    {
        $result = Http::withBasicAuth($this->secret(), '')->get(self::$baseUrl."/recipients/{$recipientId}");

        if ($result->failed()) {
            if ($result->status() == 404) {
                // @todo replace by RecipientNotExistsException
                throw new InvalidRecipientException("Recipient not exists (id: {$result})");
            }

            Log::warning('checkout-api:PagarmeRawClient:obtainRecipient:failed', [
                'http_status_code' => $result->status(),
                'http_body' => (string) $result->body(),
            ]);

            throw new ActionFailedException($result->json()['message'] ?? 'Erro desconhecido');
        }

        return $result->json();
    }

    private function secret()
    {
        return env('MUNDIPAGG_SECRET_KEY');
    }
}
