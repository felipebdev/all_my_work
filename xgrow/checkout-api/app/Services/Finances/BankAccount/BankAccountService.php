<?php

namespace App\Services\Finances\BankAccount;

use App\BankInformation;
use App\Exceptions\Finances\ActionFailedException;
use App\Exceptions\Finances\BankInformationAlreadyExistsException;
use App\Exceptions\Finances\RateLimitExceededException;
use App\Exceptions\Finances\RecipientNotFound;
use App\Exceptions\NotImplementedException;
use App\Http\Middleware\CorrelationIdHeaderMiddleware;
use App\Jobs\Finances\RevertBankModificationJob;
use App\PlatformUser;
use App\Repositories\Finances\BankInformationRepository;
use App\Repositories\Finances\RecipientUserRepository;
use App\Services\Finances\BankAccount\Contracts\BankAccountInterface;
use App\Services\Finances\BankAccount\Objects\BankAccount;
use App\Services\Finances\BankAccount\Objects\BankAccountResponse;
use App\Services\Finances\BankAccount\Objects\BankModification;
use App\Services\Finances\Recipient\RecipientManagerService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;

class BankAccountService
{

    private BankAccountInterface $bankAccount;

    private RecipientManagerService $recipientManagerService;

    private RecipientUserRepository $recipientUserRepository;

    private BankInformationRepository $bankInformationRepository;

    public function __construct(
        BankAccountAdapter $bankAccountAdapter,
        RecipientManagerService $recipientManagerService,
        RecipientUserRepository $recipientUserRepository,
        BankInformationRepository $bankInformationRepository
    ) {
        $driver = $bankAccountAdapter->driver();
        if (!$driver instanceof BankAccountInterface) {
            throw new NotImplementedException('Bank Account not implemented by driver: '.$bankAccountAdapter->getDefaultDriver());
        }

        $this->bankAccount = $driver;

        $this->recipientManagerService = $recipientManagerService;

        $this->recipientUserRepository = $recipientUserRepository;

        $this->bankInformationRepository = $bankInformationRepository;
    }

    /**
     * @param  string  $userId
     * @return \App\Services\Finances\BankAccount\Objects\BankAccountResponse
     * @throws \App\Exceptions\Finances\RecipientNotExistsException
     * @throws \App\Exceptions\Finances\RecipientNotFound
     */
    public function getDefaultUserBankAccount(string $userId): BankAccountResponse
    {
        $recipients = $this->recipientUserRepository->locateAllUserRecipients($userId);

        if (count($recipients) != 0) {
            $firstRecipient = $recipients[0];

            return $this->bankAccount->getBankAccount($firstRecipient->recipient_id);
        }

        $bankInformation = $this->bankInformationRepository->getBankInformationByUserId($userId);

        if ($bankInformation) {
            return BankAccountResponse::fromBankInformation($bankInformation);
        }

        throw new RecipientNotFound('Recipient not found for any platform');
    }

    /**
     * @param  string  $userId
     * @param  \App\Services\Finances\BankAccount\Objects\BankAccount  $bankAccount
     * @return \App\Services\Finances\BankAccount\Objects\BankAccountResponse
     * @throws \App\Exceptions\Finances\BankInformationAlreadyExistsException
     * @throws \App\Exceptions\Finances\RecipientAlreadyExistsException
     * @throws \App\Exceptions\RecipientFailedException
     */
    public function createRecipientForBankInformation(
        string $userId,
        BankAccount $bankAccount
    ): BankAccountResponse {
        $email = PlatformUser::where('id', $userId)->first()->email ?? null;

        if ($this->bankInformationRepository->hasBankInformation($userId)) {
            $bankInformation = $this->bankInformationRepository->getBankInformationByUserId($userId);
            $bankAccountResponse = BankAccountResponse::fromBankInformation($bankInformation);
        } else {
            $bankAccountResponse = $this->bankAccount->createBankAccount($bankAccount);
            $bankInformation = $this->bankInformationRepository->createBankInformation($userId, $email, $bankAccountResponse);
        }

        if ($bankInformation->bank == '000') {
            $this->updateBankInformationOriginalBank($bankInformation->id, $bankAccount->bankCode);

            Log::info('Banco cadastrado como 000', ['original_bank' => $bankAccount]);
        }

        if (is_null($bankInformation->recipient_id)) {
            $recipient = $this->recipientManagerService->createRecipientForBankInformation($bankInformation->id);
            $this->bankInformationRepository->saveBankInformationRecipient($bankInformation->id, $recipient);
        }

        return $bankAccountResponse;
    }

    /**
     * Update all recipients from user with new BankAccount settings
     *
     * Tables: clients, producers, bank_information
     *
     * @param  string  $userId
     * @param  \App\Services\Finances\BankAccount\Objects\BankAccount  $bankAccount
     * @return \App\Services\Finances\BankAccount\Objects\BankAccountResponse|null
     * @throws \App\Exceptions\Finances\ActionFailedException
     * @throws \App\Exceptions\Finances\InvalidBankAccountException
     * @throws \App\Exceptions\Finances\RecipientNotExistsException
     */
    public function changeAllUserBankAccounts(
        string $userId,
        BankAccount $bankAccount
    ): ?BankAccountResponse {
        $recipients = $this->recipientUserRepository->locateAllUserRecipientsIncludingBankInformation($userId);

        $bankModifications = [];
        try {
            foreach ($recipients as $recipient) {
                Log::withContext(["processing_{$recipient->recipient_id}" => 'initiated']);

                $bankModification = new BankModification();
                $bankModification->recipient_id = $recipient->recipient_id;
                $bankModification->original = $this->bankAccount->getBankAccount($recipient->recipient_id);
                $bankModification->modified = $this->bankAccount->updateBank($recipient->recipient_id, $bankAccount);

                Log::withContext(["processing_{$recipient->recipient_id}" => 'modified']);

                $bankModifications[] = $bankModification;

                // Fazer a alteracao do banco por aqui
                $bank_information_id = $recipient->bank_information_id ?? null;
                if ($bankModification->modified->bankCode == '000' && $bank_information_id) {
                    $this->updateBankInformationOriginalBank($bank_information_id, $bankAccount->bankCode);
                }
            }

            $firstBank = $bankModifications[0] ?? null;
            if (is_null($firstBank)) {
                return null; // no updates
            }

            if ( $firstBank->modified->bankCode == '000') {
                Log::info('Banco cadastrado como 000', ['original_bank' => $bankAccount, 'total_affected' => count($bankModifications)]);
            }

            foreach ($recipients as $recipient) {
                $this->recipientUserRepository->updateBankDataByRecipientId($recipient->recipient_id, $firstBank->modified);
            }

            return $firstBank->modified;
        } catch (Exception $exception) {
            if (app()->bound('sentry')) {
                app('sentry')->captureException($exception);
            }

            \Facades\App\Helpers\Log::exception('checkout:finances:bank-accounts:exception', $exception);

            $this->undoBankAccountModifications($bankModifications);

            $startUuid = CorrelationIdHeaderMiddleware::getStart();
            throw new ActionFailedException("[código de rastreio {$startUuid}]");
        }
    }

    /**
     * @param  array<BankModification>  $bankModifications
     */
    private function undoBankAccountModifications(array $bankModifications): void
    {
        foreach ($bankModifications as $index => $bankModification) {
            $delay = 30 * ($index + 1); // delay for each revert: first = 30s, second = 60s, thirdy = 90s, etc

            RevertBankModificationJob::dispatch($bankModification)
                ->delay(Carbon::now()->addSeconds($delay));
        }
    }

    /**
     * @param  \App\Services\Finances\BankAccount\Objects\BankModification  $bankModification
     * @return \App\Services\Finances\BankAccount\Objects\BankAccountResponse
     * @throws \App\Exceptions\Finances\InvalidBankAccountException
     * @throws \App\Exceptions\Finances\RateLimitExceededException
     * @throws \App\Exceptions\Finances\RecipientNotExistsException
     */
    public function revertSingleBankAccountModification(BankModification $bankModification): BankAccountResponse
    {
        Log::debug('checkout:finances:bank-accounts:reverting', [
            'bank_modification' => $bankModification,
        ]);

        $bankAccount = BankAccount::fromBankAccountResponse($bankModification->original);

        $reverted = $this->bankAccount->updateBank($bankModification->recipient_id, $bankAccount);

        Log::debug('checkout:finances:bank-accounts:reverted', [
            'bank_reverted' => $reverted,
        ]);

        $this->recipientUserRepository->updateBankDataByRecipientId($bankModification->recipient_id, $reverted);

        Log::debug('checkout:finances:bank-data:reverted');

        return $reverted;
    }

    private function updateBankInformationOriginalBank(Int $bankInformationId, String $bankCode): void
    {
        BankInformation::query()
            ->where('id', $bankInformationId)
            ->update([
                'original_bank' => $bankCode
            ]);
    }

}
