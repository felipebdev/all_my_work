<?php

namespace App\Services\Finances\BankAccount\Drivers;

use App\BankInformation;
use App\Exceptions\Finances\InvalidBankAccountException;
use App\Exceptions\Finances\RateLimitExceededException;
use App\Exceptions\Finances\RecipientNotExistsException;
use App\Services\Finances\BankAccount\Contracts\BankAccountInterface;
use App\Services\Finances\BankAccount\Objects\BankAccount;
use App\Services\Finances\BankAccount\Objects\BankAccountResponse;
use App\Services\Finances\Objects\Constants;
use App\Services\MundipaggService;
use Illuminate\Support\Facades\Log;
use MundiAPILib\Exceptions\ErrorException;
use PagarMe\Client;
use PagarMe\Exceptions\PagarMeException;

class PagarmeBankAccount implements BankAccountInterface
{

    protected Client $pagarme;
    protected MundipaggService $mundipaggService;

    private array $accountTypeToPagarme = [
        Constants::XGROW_ACCOUNT_TYPE_CHECKING => Constants::PAGARME_ACCOUNT_TYPE_CHECKING,
        Constants::XGROW_ACCOUNT_TYPE_SAVINGS => Constants::PAGARME_ACCOUNT_TYPE_SAVINGS,
    ];

    public function __construct()
    {
        $this->pagarme = new Client(env('PAGARME_API_KEY'));
        $this->mundipaggService = new MundipaggService();
    }

    public function getBankAccount(string $mundipaggRecipientId): BankAccountResponse
    {
        $pagarmeId = $this->mundipaggService->convertToPagarMeRecipientId($mundipaggRecipientId);

        if (is_null($pagarmeId)) {
            throw new RecipientNotExistsException("Recipient not exists (id: {$mundipaggRecipientId})");
        }

        try {
            $result = (object) $this->pagarme->recipients()->get([
                'id' => $pagarmeId,
            ]);

            return BankAccountResponse::fromPagarmeObject($result->bank_account);
        } catch (PagarMeException | ErrorException $e) {
            throw new RecipientNotExistsException("Recipient not exists (id: {$pagarmeId})");
        }
    }

    public function createBankAccount(BankAccount $bankAccount): BankAccountResponse
    {
        try {
            $agencyDigit = strlen($bankAccount->agencyDigit) > 0 ? $bankAccount->agencyDigit : null; // empty to null

            $banksAllowed = BankInformation::$banksAllowed;

            $payload = [
                'bank_code' => (in_array($bankAccount->bankCode, $banksAllowed)) ? $bankAccount->bankCode : '000',
                'agencia' => $bankAccount->agency,
                'agencia_dv' => $agencyDigit,
                'conta' => $bankAccount->account,
                'conta_dv' => $bankAccount->accountDigit,
                'type' => $this->accountTypeToPagarme[$bankAccount->accountType] ?? null,
                'document_number' => $bankAccount->documentNumber,
                'legal_name' => $bankAccount->legalName,
            ];

            Log::debug('Create Bank Account Payload', ['payload' => $payload]);

            $bankResponse = $this->pagarme->bankAccounts()->create($payload);

            return BankAccountResponse::fromPagarmeObject((object) $bankResponse);

        } catch (PagarMeException $exception) {
            Log::error('Create Bank Account Error', [
                'Bank_Account' => $bankAccount,
                'exception' => [
                    'message' => $exception->getMessage(),
                    'code' => $exception->getCode(),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    'trace' => $exception->getTraceAsString(),
                ],
            ]);

            throw new InvalidBankAccountException($exception->getMessage());
        }
    }

    /**
     * @param  string  $mundipaggRecipientId
     * @param  \App\Services\Finances\BankAccount\Objects\BankAccount  $bankAccount
     * @return \App\Services\Finances\BankAccount\Objects\BankAccountResponse
     * @throws \App\Exceptions\Finances\InvalidBankAccountException
     * @throws \App\Exceptions\Finances\RateLimitExceededException
     * @throws \App\Exceptions\Finances\RecipientNotExistsException
     * @throws \ErrorException
     * @throws \MundiAPILib\Exceptions\ErrorException
     */
    public function updateBank(string $mundipaggRecipientId, BankAccount $bankAccount): BankAccountResponse
    {
        $pagarmeId = $this->mundipaggService->convertToPagarMeRecipientId($mundipaggRecipientId);
        if (is_null($pagarmeId)) {
            throw new RecipientNotExistsException("Recipient not exists (id: {$mundipaggRecipientId})");
        }

        try {
            $agencyDigit = strlen($bankAccount->agencyDigit) > 0 ? $bankAccount->agencyDigit : null; // empty to null

            $banksAllowed = BankInformation::$banksAllowed;

            $updatedRecipient = $this->pagarme->recipients()->update([
                'id' => $pagarmeId,
                'bank_account' => [
                    'bank_code' => (in_array($bankAccount->bankCode, $banksAllowed)) ? $bankAccount->bankCode : '000',
                    'agencia' => $bankAccount->agency,
                    'agencia_dv' => $agencyDigit,
                    'conta' => $bankAccount->account,
                    'conta_dv' => $bankAccount->accountDigit,
                    'type' => $this->accountTypeToPagarme[$bankAccount->accountType] ?? null,
                    'document_number' => $bankAccount->documentNumber,
                    'legal_name' => $bankAccount->legalName,
                ]
            ]);

            return BankAccountResponse::fromPagarmeObject($updatedRecipient->bank_account);
        } catch (PagarMeException $e) {
            throw new InvalidBankAccountException($e->getMessage());
        } catch (\ErrorException $e) {
            if ($e->getMessage() == 'Undefined property: stdClass::$errors') {
                // Wrap Pagar.me SDK bug on handling "Rate limit exceeded" (https://xgrow.atlassian.net/browse/XP-3255)
                throw new RateLimitExceededException('Rate limit exceeded');
            }

            throw $e; // rethrow original if is another type of ErrorException
        }
    }

}
