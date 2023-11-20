<?php

namespace App\Repositories\Finances;

use App\BankInformation;
use App\Client;
use App\Producer;
use App\Services\Finances\BankAccount\Objects\BankAccountResponse;
use App\Services\Finances\Recipient\Objects\RecipientResponse;
use Carbon\Carbon;

class BankInformationRepository
{
    public function getBankInformationByUserId(string $userId): ?BankInformation
    {
        $bankInformation = BankInformation::query()
            ->where('platform_user_id', $userId)
            ->where('used', 0)
            ->first();

        return $bankInformation;
    }

    /**
     * @param  string  $userId
     * @return bool
     */
    public function hasBankInformation(string $userId): bool
    {
        return BankInformation::query()
            ->where('platform_user_id', $userId)
            ->where('used', 0)
            ->exists();
    }

    /**
     * @param  string  $userId
     * @param  string|null  $email
     * @param  BankAccountResponse  $bankAccountResponse
     * @return BankInformation
     */
    public function createBankInformation(string $userId, ?string $email, BankAccountResponse $bankAccountResponse): BankInformation
    {
        return BankInformation::query()->create([
            'platform_user_id' => $userId,
            'email' => $email,
            'document_type' => $bankAccountResponse->documentType,
            'document' => $bankAccountResponse->documentNumber,
            'holder_name' => $bankAccountResponse->legalName,
            'account_type' => $bankAccountResponse->accountType,
            'bank' => $bankAccountResponse->bankCode,
            'branch' => $bankAccountResponse->agency,
            'account' => $bankAccountResponse->account,
            'branch_check_digit' => $bankAccountResponse->agencyDigit,
            'account_check_digit' => $bankAccountResponse->accountDigit,
            'gateway_bank_id' => "{$bankAccountResponse->getRawData()->id}",
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }

    /**
     * Add recipient data to bank information
     *
     * @param  int  $bankInformationId
     * @param  \App\Services\Finances\Recipient\Objects\RecipientResponse  $recipient
     * @return int
     */
    public function saveBankInformationRecipient(int $bankInformationId, RecipientResponse $recipient): int
    {
        return BankInformation::query()
            ->where('id', $bankInformationId)
            ->update([
                'recipient_id' => $recipient->getId(),
                'recipient_status' => $recipient->getStatus(),
                'recipient_reason' => $recipient->getReason(),
            ]);
    }

    /**
     * Copy Bank Information to Client
     *
     * @param  \App\BankInformation  $bankInformation
     * @param  \App\Client  $client
     * @return bool
     */
    public function copyBankInformationToClient(BankInformation $bankInformation, Client $client): bool
    {
        $clientUpdate = [
            'bank' => $bankInformation->bank,
            'branch' => $bankInformation->branch,
            'account' => $bankInformation->account,
            'recipient_id' => $bankInformation->recipient_id,
            'holder_name' => $bankInformation->holder_name,
            'account_type' => $bankInformation->account_type,
            'branch_check_digit' => $bankInformation->branch_check_digit,
            'account_check_digit' => $bankInformation->account_check_digit,
        ];

        return $client->update($clientUpdate);
    }

    /**
     * Copy Bank Information to `producer` table
     *
     * @param  \App\BankInformation  $bankInformation
     * @param  \App\Producer  $producer
     * @return bool
     */
    public function copyBankInformationToProducer(BankInformation $bankInformation, Producer $producer): bool
    {
        $producerUpdate = [
            'bank' => $bankInformation->bank,
            'branch' => $bankInformation->branch,
            'account' => $bankInformation->account,
            'recipient_id' => $bankInformation->recipient_id,
            'recipient_status' => $bankInformation->recipient_status,
            'recipient_reason' => $bankInformation->recipient_reason,
            'holder_name' => $bankInformation->holder_name,
            'account_type' => $bankInformation->account_type,
            'branch_check_digit' => $bankInformation->branch_check_digit,
            'account_check_digit' => $bankInformation->account_check_digit,
        ];

        return $producer->update($producerUpdate);
    }

}
