<?php

namespace App\Services\Finances\Recipient;

use App\BankInformation;
use App\Client;
use App\Config;
use App\Exceptions\Finances\RecipientAlreadyExistsException;
use App\Exceptions\NotImplementedException;
use App\Exceptions\RecipientFailedException;
use App\Platform;
use App\PlatformUser;
use App\Producer;
use App\Repositories\Finances\BankInformationRepository;
use App\Services\Finances\Recipient\Contracts\RecipientManagerInterface;
use App\Services\Finances\Recipient\Objects\RecipientResponse;
use App\Services\Mundipagg\Objects\RecipientData;
use Illuminate\Support\Facades\Log;

/**
 * This class handles business rules related to creation, retrieval and editing of recipients.
 */
class RecipientManagerService
{

    private RecipientManagerInterface $recipientManager;

    private BankInformationRepository $bankInformationRepository;

    public function __construct(
        RecipientManagerAdapter $adapter,
        BankInformationRepository $bankInformationRepository
    ) {
        $driver = $adapter->driver();
        if (!$driver instanceof RecipientManagerInterface) {
            throw new NotImplementedException('Recipient manager not implemented by driver: '.$adapter->getDefaultDriver());
        }

        $this->recipientManager = $driver;

        $this->bankInformationRepository = $bankInformationRepository;
    }

    /**
     * @param  int  $bankInformationId
     * @return \App\Services\Finances\Recipient\Objects\RecipientResponse
     * @throws \App\Exceptions\Finances\RecipientAlreadyExistsException
     * @throws \App\Exceptions\RecipientFailedException
     */
    public function createRecipientForBankInformation(int $bankInformationId): RecipientResponse
    {
        $bankInformation = BankInformation::find($bankInformationId);

        if ($bankInformation->recipient_id) {
            Log::warning('Recipient already exists for this bank information');
            throw new RecipientAlreadyExistsException();
        }

        $recipientData = RecipientData::fromBankInformation($bankInformation);

        $recipient = $this->recipientManager->createRecipient($recipientData);

        return $recipient;
    }

    /**
     * Create a new recipient for Xgrow using data from `config` table.
     *
     * @return \App\Services\Finances\Recipient\Objects\RecipientResponse
     * @throws \App\Exceptions\Finances\RecipientAlreadyExistsException
     */
    public function createXgrowRecipientUsingFirstConfigAndStore(): RecipientResponse
    {
        $config = Config::first();

        if ($config->recipient_id) {
            Log::warning('Xgrow recipient already exists');
            throw new RecipientAlreadyExistsException();
        }

        $recipientData = RecipientData::fromConfig($config);
        $recipient = $this->recipientManager->createRecipient($recipientData);

        $config->recipient_id = $recipient->getId();
        $config->save();

        return $recipient;
    }

    /**
     * @param  string  $platformId
     * @param  string  $userId
     * @param  string  $actingAs
     * @return \App\Services\Finances\Recipient\Objects\RecipientResponse
     * @throws \App\Exceptions\Finances\RecipientAlreadyExistsException
     * @throws \App\Exceptions\RecipientFailedException
     */
    public function createRecipientAndStore(string $platformId, string $userId, string $actingAs): RecipientResponse
    {
        Log::withContext(['platformId' => $platformId, 'userId' => $userId, 'acting_as' => $actingAs]);

        if ($actingAs == 'client') {
            $platform = Platform::findOrFail($platformId);

            return $this->createPlatformRecipientAndStore($platform, $userId);
        }

        $producer = Producer::where('platform_id', $platformId)
            ->where('platform_user_id', $userId)
            ->first();

        if ($producer) {
            return $this->createProducerRecipientAndStore($producer);
        }

        throw new RecipientFailedException('Usuário da plataforma não existe');
    }

    /**
     * Create a new Platform recipient using data from tables.
     *
     * @param  \App\Platform  $platform
     * @param  string  $userId
     * @return \App\Services\Finances\Recipient\Objects\RecipientResponse
     * @throws \App\Exceptions\Finances\RecipientAlreadyExistsException
     * @throws \App\Exceptions\RecipientFailedException
     */
    public function createPlatformRecipientAndStore(Platform $platform, string $userId): RecipientResponse
    {
        Log::withContext(['platformId' => $platform->id ?? null]);
        Log::withContext(['userId' => $userId ?? null]);
        $client = $platform->client;

        if ($platform->recipient_id) {
            Log::warning('Platform recipient already exists');
            throw new RecipientAlreadyExistsException();
        }

        $accounts = BankInformation::query()
            ->where('platform_user_id', $userId)
            ->get();

        $firstAccount = $accounts->first();

        $accountNotUsed = $accounts->firstWhere('used', 0);

        if ($accounts->count() == 0) {
            Log::debug('checkout:finances:recipient:client:new:from_platform_and_client');

            $recipientData = RecipientData::fromPlatformAndClient($platform, $client);

            $recipient = $this->recipientManager->createRecipient($recipientData);
        } elseif ($accountNotUsed) {
            Log::debug('checkout:finances:recipient:client:use_existing');

            $this->bankInformationRepository->copyBankInformationToClient($accountNotUsed, $client);

            $accountNotUsed->update(['used' => 1]);

            $recipient = RecipientResponse::fromBankInformation($accountNotUsed);
        } else {
            Log::debug('checkout:finances:recipient:client:new:from_bank_information');

            $recipientData = RecipientData::fromBankInformation($firstAccount);

            $recipient = $this->recipientManager->createRecipient($recipientData);

            $this->bankInformationRepository->copyBankInformationToClient($firstAccount, $client);
        }

        $platform->recipient_id = $recipient->getId();
        $platform->recipient_status = $recipient->getStatus() ?? null;
        // $platform->recipient_gateway = 'mundipagg';
        $platform->save();

        Log::withContext(['recipient_id' => $recipient->getId() ?? null]);
        Log::debug('checkout:finances:recipient:client:vinculated');

        return $recipient;
    }

    /**
     * @param  \App\Producer  $producer
     * @return \App\Services\Finances\Recipient\Objects\RecipientResponse
     * @throws \App\Exceptions\Finances\RecipientAlreadyExistsException
     * @throws \App\Exceptions\RecipientFailedException
     */
    public function createProducerRecipientAndStore(Producer $producer): RecipientResponse
    {
        Log::withContext(['producer_id' => $producer->id ?? null]);

        if ($producer->recipient_id) {
            Log::warning('Producer recipient already exists');
            throw new RecipientAlreadyExistsException();
        }

        $userId = $producer->platform_user_id;

        $accounts = BankInformation::query()
            ->where('platform_user_id', $userId)
            ->get();

        $firstAccount = $accounts->first();

        $banksAllowed = BankInformation::$banksAllowed;

        if ($firstAccount) {
            if ( !in_array( $firstAccount->bank, $banksAllowed ) ) {
                Log::info('Banco cadastrado como 000', ['original_bank' => $firstAccount]);

                $firstAccount->update([
                    'bank' => '000',
                    'original_bank' => $firstAccount->bank
                ]);
            }
        }

        $accountNotUsed = $accounts->firstWhere('used', 0);

        if ($accounts->count() == 0) {
            Log::info('checkout:finances:recipient:producer:new:from_platform_and_client');

            $this->updateProducerFromClientData($producer);

            $recipientData = RecipientData::fromProducer($producer);

            $recipient = $this->recipientManager->createRecipient($recipientData);
        } elseif ($accountNotUsed) {
            Log::info('checkout:finances:recipient:producer:use_existing');

            $this->bankInformationRepository->copyBankInformationToProducer($accountNotUsed, $producer);

            $accountNotUsed->update(['used' => 1]);

            $recipient = RecipientResponse::fromBankInformation($accountNotUsed);
        } else {
            Log::info('checkout:finances:recipient:producer:new:from_bank_information');

            $recipientData = RecipientData::fromBankInformation($firstAccount);

            $recipient = $this->recipientManager->createRecipient($recipientData);

            $this->bankInformationRepository->copyBankInformationToProducer($firstAccount, $producer);
        }

        $producer->recipient_id = $recipient->getId();
        $producer->recipient_status = $recipient->getStatus() ?? null;
        $producer->recipient_gateway = 'mundipagg';
        $producer->save();

        Log::withContext(['recipient_id' => $recipient->getId() ?? null]);
        Log::info('checkout:finances:recipient:producer:vinculated');

        return $recipient;
    }

    /**
     * @param  string  $recipientId
     * @return \App\Services\Finances\Recipient\Objects\RecipientResponse
     * @throws \App\Exceptions\Finances\ActionFailedException
     * @throws \App\Exceptions\Finances\InvalidRecipientException
     */
    public function obtainRecipient(string $recipientId): RecipientResponse
    {
        return $this->recipientManager->obtainRecipient($recipientId);
    }

    private function updateProducerFromClientData(Producer $producer): Producer
    {
        $platformUser = PlatformUser::find($producer->platform_user_id);

        $client = Client::firstWhere('email', $platformUser->email);

        $documentType = $client->type_person == 'F' ? 'cpf' : 'cnpj';
        $document = $client->type_person == 'F' ? $client->cpf : $client->cnpj;

        $producer->holder_name ??= $client->holder_name;
        $producer->document_type ??= $documentType;
        $producer->document ??= $document;
        $producer->holder_name ??= $client->holder_name;
        $producer->account_type ??= $client->account_type;
        $producer->bank ??= $client->bank;
        $producer->branch ??= $client->branch;
        $producer->account ??= $client->account;
        $producer->branch_check_digit ??= $client->branch_check_digit;
        $producer->account_check_digit ??= $client->account_check_digit;

        $producer->save();

        return $producer;
    }

}
