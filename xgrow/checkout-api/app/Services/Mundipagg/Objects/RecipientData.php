<?php

namespace App\Services\Mundipagg\Objects;

use App\BankInformation;
use App\Client;
use App\Config;
use App\Exceptions\RecipientFailedException;
use App\Platform;
use App\PlatformUser;
use App\Producer;
use App\Services\Finances\Objects\Constants;
use Illuminate\Support\Facades\Log;

class RecipientData
{
    public static function fromPlatformAndClient(Platform $platform, Client $client): self
    {
        $firstName = $client->first_name ?? '';
        $lastName = $client->last_name ?? '';
        $platformName = $platform->name ?? '';
        $companyName = $client->company_name ?? '';

        $recipientData = new RecipientData();
        $recipientData->name = "{$firstName} {$lastName} - {$platformName}";
        $recipientData->email = $client->email;
        $recipientData->description = "{$companyName} - {$platformName}";
        $recipientData->document = $client->type_person == Client::TYPE_PERSON_PHYSICAL
            ? self::cleanSubstr($client->cpf)
            : self::cleanSubstr($client->cnpj);
        $recipientData->holderName = $client->holder_name;
        $recipientData->bank = $client->bank;
        $recipientData->branchNumber = self::cleanSubstr($client->branch);
        $recipientData->branchCheckDigit = self::cleanSubstr($client->branch_check_digit);
        $recipientData->accountType = $client->account_type;
        $recipientData->accountNumber = self::cleanSubstr($client->account);
        $recipientData->accountCheckDigit = self::cleanSubstr($client->account_check_digit);
        $recipientData->metadata = [
            'client_id' => $client->id,
            'platform_id' => $platform->id
        ];

        return $recipientData;
    }

    public static function fromConfig(Config $config): self
    {
        $recipientData = new RecipientData();
        $recipientData->name = $config->name;
        $recipientData->email = $config->email;
        $recipientData->description = "Fandone";
        $recipientData->document = self::cleanSubstr($config->document);
        $recipientData->holderName = $config->name;
        $recipientData->bank = $config->bank;
        $recipientData->branchNumber = self::cleanSubstr($config->branch, 0, -1);
        $recipientData->branchCheckDigit = self::cleanSubstr($config->branch, -1, 1);
        $recipientData->accountType = Constants::XGROW_ACCOUNT_TYPE_CHECKING;
        $recipientData->accountNumber = self::cleanSubstr($config->account, 0, -1);
        $recipientData->accountCheckDigit = self::cleanSubstr($config->account, -1, 1);

        return $recipientData;
    }

    public static function fromBankInformation(BankInformation $bankInformation): self
    {
        $recipientData = new RecipientData();
        $recipientData->name = $bankInformation->holder_name;

        $recipientData->email = $bankInformation->email;
        $recipientData->description = "{$bankInformation->holder_name} - {$bankInformation->email}";
        $recipientData->document = self::cleanSubstr($bankInformation->document);
        $recipientData->holderName = "{$bankInformation->holder_name}";
        $recipientData->bank = $bankInformation->bank;
        $recipientData->branchNumber = self::cleanSubstr($bankInformation->branch);
        $recipientData->branchCheckDigit = self::cleanSubstr($bankInformation->branch_check_digit);
        $recipientData->accountType = $bankInformation->account_type;
        $recipientData->accountNumber = self::cleanSubstr($bankInformation->account);
        $recipientData->accountCheckDigit = self::cleanSubstr($bankInformation->account_check_digit);

        return $recipientData;
    }

    /**
     * @param  \App\Producer  $producer
     * @return static
     * @throws \App\Exceptions\RecipientFailedException
     */
    public static function fromProducer(Producer $producer): self
    {
        $platformName = $producer->platform->name ?? '';

        $platformUser = PlatformUser::findOrFail($producer->platform_user_id); // @todo: fix relationship on model
        $email = $platformUser->email;

        self::validateProducer($email, $producer);

        $recipientData = new self();
        $recipientData->name = $producer->holder_name;

        $recipientData->email = $email;
        $recipientData->description = "{$producer->holder_name} - {$platformName}";
        $recipientData->document = self::cleanSubstr($producer->document);
        $recipientData->holderName = "{$producer->holder_name}";
        $recipientData->bank = $producer->bank;
        $recipientData->branchNumber = self::cleanSubstr($producer->branch);
        $recipientData->branchCheckDigit = self::cleanSubstr($producer->branch_check_digit);
        $recipientData->accountNumber = self::cleanSubstr($producer->account);
        $recipientData->accountCheckDigit = self::cleanSubstr($producer->account_check_digit);
        $recipientData->accountType = $producer->account_type;

        return $recipientData;
    }

    /**
     * @param  string  $email
     * @param  \App\Producer  $producer
     * @throws \App\Exceptions\RecipientFailedException
     */
    private static function validateProducer(string $email, Producer $producer)
    {
        $errors = [];
        if (!$producer->holder_name) {
            $errors[] = 'Missing holder name';
        }

        if (!$producer->document) {
            $errors[] = 'Missing document';
        }

        if (!$producer->bank) {
            $errors[] = 'Missing bank';
        }

        if (!$producer->branch) {
            $errors[] = 'Missing branch';
        }

        if (!$producer->account) {
            $errors[] = 'Missing account';
        }

        if ($errors) {

            Log::error('Failed to validate producer/afiliate', [
                'email' => $email ?? '',
                'producer' => $producer->toArray() ?? [],
                'errors' => $errors ?? [],
            ]);

            self::throwRecipientFailedException($email, $producer->holder_name ?? null);
        }
    }

    public string $platformId;
    public string $name;
    public string $email;
    public string $description;
    public string $holderName;
    public string $document;
    public string $bank;
    public string $branchNumber;
    public ?string $branchCheckDigit = null;
    public string $accountNumber;
    public string $accountCheckDigit;
    public string $accountType;
    public array $metadata = [];

    /**
     * Get substring after cleaning up a string to only digits
     *
     * @param $text
     * @param  int  $offset
     * @param  int  $length
     * @return false|string
     */
    private static function cleanSubstr($text, int $offset = 0, int $length = PHP_INT_MAX)
    {
        $onlyDigits = preg_replace('/[^0-9]/', '', $text);
        return substr($onlyDigits, $offset, $length);
    }

    private static function throwRecipientFailedException(string $email, $holderName): void
    {
        $user = $holderName ?? $email;
        $message = "{$user} não finalizou a verificação dos seus documentos/dados bancários.";

        $exception = new RecipientFailedException($message);
        $exception->withFailures([
            [
                'last_four_digits' => null,
                'brand' => null,
                'code' => '9999',
                'message' => "Erro ao criar o recebedor (coprodutor/afiliado)",
                'friendly_message' => "Coprodutor {$user} não finalizou a verificação dos seus documentos/dados bancários.",
            ]
        ]);

        throw $exception;
    }

}
