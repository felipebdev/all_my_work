<?php

namespace App\Repositories\Finances;

use App\BankInformation;
use App\Client;
use App\Platform;
use App\PlatformUser;
use App\Producer;
use App\Services\Finances\BankAccount\Objects\BankAccountResponse;

class RecipientUserRepository
{

    public function locateAllUserRecipients(string $userId): array
    {
        $recipients = [];

        // by platform
        $platformUser = PlatformUser::find($userId);
        $client = Client::where('email', $platformUser->email)->first();

        if ($client) {
            $platforms = Platform::query()
                ->where('customer_id', $client->id)
                ->get();

            foreach ($platforms as $platform) {
                $platformRecipientId = $platform->recipient_id ?? null;
                if (!$platformRecipientId) {
                    continue;
                }

                $recipients[] = (object) [
                    'recipient_id' => $platformRecipientId,
                    'bank_information_id' => null,
                    'type' => RecipientRepository::RECIPIENT_TYPE_CLIENT,
                ];
            }
        }

        // producers
        $producerRecipients = Producer::query()
            ->where('platform_user_id', $userId)
            ->where('type', Producer::TYPE_PRODUCER)
            ->get();

        foreach ($producerRecipients as $producerRecipient) {
            $producerRecipientId = $producerRecipient->recipient_id ?? null;
            if (!$producerRecipientId) {
                continue;
            }

            $recipients[] = (object) [
                'recipient_id' => $producerRecipientId,
                'type' => RecipientRepository::RECIPIENT_TYPE_PRODUCER,
            ];
        }

        // affiliates
        $affiliateRecipients = Producer::query()
            ->where('platform_user_id', $userId)
            ->where('type', Producer::TYPE_AFFILIATE)
            ->get();

        foreach ($affiliateRecipients as $affiliateRecipient) {
            $affiliateRecipientId = $affiliateRecipient->recipient_id ?? null;
            if (!$affiliateRecipientId) {
                continue;
            }

            $recipients[] = (object) [
                'recipient_id' => $affiliateRecipientId,
                'type' => RecipientRepository::RECIPIENT_TYPE_AFFILIATE,
            ];
        }

        return $recipients;
    }

    public function locateAllUserRecipientsIncludingBankInformation(string $userId): array
    {
        // clients, co-producers, affiliates
        $recipients = $this->locateAllUserRecipients($userId);

        // recipients from bank information
        $bankInformationList = BankInformation::query()
            ->where('platform_user_id', $userId)
            ->get();

        foreach ($bankInformationList as $bankInformation) {
            $bankInformationRecipientId = $bankInformation->recipient_id ?? null;
            if (!$bankInformationRecipientId) {
                continue;
            }

            $recipients[] = (object) [
                'recipient_id' => $bankInformationRecipientId,
                'bank_information_id' => $bankInformation->id,
                'type' => RecipientRepository::RECIPIENT_TYPE_BANK_INFORMATION,
            ];
        }

        return $recipients;
    }


    /**
     * Update bank data (platform/producer/affiliate) for a given recipient
     *
     * @param  string  $recipientId
     * @param  \App\Services\Finances\BankAccount\Objects\BankAccountResponse  $bankAccountResponse
     */
    public function updateBankDataByRecipientId(string $recipientId, BankAccountResponse $bankAccountResponse)
    {
        $baseUpdate = [
            'holder_name' => $bankAccountResponse->legalName,
            'account_type' => $bankAccountResponse->accountType,
            'bank' => $bankAccountResponse->bankCode,
            'branch' => $bankAccountResponse->agency,
            'branch_check_digit' => $bankAccountResponse->agencyDigit,
            'account' => $bankAccountResponse->account,
            'account_check_digit' => $bankAccountResponse->accountDigit,
        ];

        $cnpjCpf = $bankAccountResponse->documentType;

        $mainUpdate = array_merge($baseUpdate, [
            'document' => $bankAccountResponse->documentNumber,
            'document_type' => $cnpjCpf,
        ]);

        // update bank_information
        BankInformation::where('recipient_id', $recipientId)->update($mainUpdate);

        // update producer/affiliate bank data
        Producer::where('recipient_id', $recipientId)->update($mainUpdate);

        $updateClient = array_merge($baseUpdate, [
            $cnpjCpf => $bankAccountResponse->documentNumber, // cpf/cnpj column
        ]);

        // update platform bank data
        Platform::where('recipient_id', $recipientId)->get()->each(function (Platform $platform) use ($updateClient) {
            $platform->client()->update($updateClient);
        });
    }
}
