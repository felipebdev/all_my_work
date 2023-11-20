<?php

namespace App\Services\Mundipagg;

use App\Http\Controllers\Controller;
use App\Services\Finances\Objects\Constants;
use App\Services\Mundipagg\Objects\RecipientData;
use App\Services\MundipaggService;
use App\Services\Pagarme\PagarmeRawClient;
use Exception;
use Illuminate\Support\Str;
use MundiAPILib\Models\CreateBankAccountRequest;
use MundiAPILib\Models\CreateRecipientRequest;
use MundiAPILib\Models\CreateTransferSettingsRequest;
use MundiAPILib\Models\UpdateAutomaticAnticipationSettingsRequest;

/**
 * Recipient Service translates the RecipientData to the acceptable format on Mundipagg
 *
 * @deprecated On sprint 0.34, use {@see \App\Services\Finances\Recipient\Drivers\PagarmeV5RecipientManager} instead
 */
class RecipientService extends Controller
{

    public $recipientsController;

    private PagarmeRawClient $pagarmeRawClient;

    public function __construct()
    {
        $mundipaggService = new MundipaggService();
        $client = $mundipaggService->getClient();
        $this->recipientsController = $client->getRecipients();
        $this->pagarmeRawClient = new PagarmeRawClient();
    }

    /**
     * @param  \App\Services\Mundipagg\Objects\RecipientData  $data
     * @return \stdClass
     * @throws \Exception
     *
     * @deprecated On sprint 0.34, use
     * {@see \App\Services\Finances\Recipient\Drivers\PagarmeV5RecipientManager::createRecipient()} instead
     */
    public function create(RecipientData $data)
    {
        $this->validateData($data);

        $resultRecipient = $this->pagarmeRawClient->createRecipient($data);

        return $resultRecipient;
    }

    /**
     * @param  \App\Services\Mundipagg\Objects\RecipientData  $data
     * @return mixed
     * @throws \MundiAPILib\APIException
     *
     * @deprecated On sprint 0.34
     */
    public function createUsingSdk(RecipientData $data)
    {
        $this->validateData($data);

        $branchCheckDigit = strlen($data->branchCheckDigit) > 0 ? $data->branchCheckDigit : null; // empty to null

        $recipient = new CreateRecipientRequest();
        $recipient->name = Str::limit($data->name, 128, '');
        $recipient->email = Str::limit($data->email, 64, '');
        $recipient->description = Str::limit($data->description, 256, '');
        $recipient->document = $data->document;
        $recipient->type = "individual";
        $recipient->defaultBankAccount = new CreateBankAccountRequest();
        $recipient->defaultBankAccount->holderName = Str::limit($data->holderName, 30, '');
        $recipient->defaultBankAccount->holderType = "individual";
        $recipient->defaultBankAccount->holderDocument = $data->document;
        $recipient->defaultBankAccount->bank = $data->bank;
        $recipient->defaultBankAccount->branchNumber = $data->branchNumber;
        $recipient->defaultBankAccount->branchCheckDigit = $branchCheckDigit;
        $recipient->defaultBankAccount->accountNumber = $data->accountNumber;
        $recipient->defaultBankAccount->accountCheckDigit = $data->accountCheckDigit;
        $recipient->defaultBankAccount->type = Constants::MUNDIPAGG_ACCOUNT_TYPE_CHECKING;
        $recipient->transferSettings = new CreateTransferSettingsRequest();
        $recipient->transferSettings->transferEnabled = false;
        $recipient->transferSettings->transferInterval = "Daily";
        $recipient->transferSettings->transferDay = "0";

        $resultRecipient = $this->recipientsController->createRecipient($recipient);

        return $resultRecipient;
    }

    /**
     * @param $data
     * @throws \Exception
     *
     * @deprecated On sprint 0.34
     */
    public function validateData($data)
    {
        $fields = ['name', 'email', 'description', 'document', 'bank', 'branchNumber', 'accountNumber', 'accountCheckDigit'];
        foreach ($fields as $field) {
            if (!strlen($data->{$field}) > 0) {
                throw new Exception("O campo $field deve ser preenchido. Verifique os dados bancários no cadastro do cliente");
            }
        }
    }

    /**
     * @param $recipientId
     * @throws \MundiAPILib\APIException
     *
     * @deprecated On sprint 0.34
     */
    public function configureAnticipation($recipientId): void
    {
        $automaticAntecipationSetting = new UpdateAutomaticAnticipationSettingsRequest();
        $automaticAntecipationSetting->enabled = true;
        $automaticAntecipationSetting->type = '1025';
        $automaticAntecipationSetting->volumePercentage = 100; //Volume disponível para antecipar (%)
        $automaticAntecipationSetting->days = [
            1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 27, 28,
            30, 31
        ];
        $automaticAntecipationSetting->delay = '29'; //se for D+30 o delay fica em 29.
        $automaticAntecipation = $this->recipientsController
            ->updateAutomaticAnticipationSettings($recipientId, $automaticAntecipationSetting);
    }
}
