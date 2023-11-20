<?php

namespace App\Services\Mundipagg;

use App\Http\Controllers\Controller;
use App\Services\MundipaggService;
use Exception;
use MundiAPILib\Models\CreateBankAccountRequest;
use MundiAPILib\Models\CreateRecipientRequest;
use MundiAPILib\Models\CreateTransferSettingsRequest;
use MundiAPILib\Models\UpdateAutomaticAnticipationSettingsRequest;

class RecipientService extends Controller
{

    public $ordersController;
    public $recipientsController;
    
    public function __construct($platform_id)
    {
        $mundipaggService = new MundipaggService($platform_id);
        $client = $mundipaggService->getClient();
        $this->ordersController = $client->getOrders();
        $this->recipientsController = $client->getRecipients();

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($data)
    {
        $this->validateData($data);

        $recipient = new CreateRecipientRequest();
        $recipient->name = $data->name;
        $recipient->email = $data->email;
        $recipient->description = $data->description;
        $recipient->document = $data->document;
        $recipient->type = "individual";
        $recipient->defaultBankAccount = new CreateBankAccountRequest();
        $recipient->defaultBankAccount->holderName = $data->name;
        $recipient->defaultBankAccount->holderType = "individual";
        $recipient->defaultBankAccount->holderDocument = $data->document;
        $recipient->defaultBankAccount->bank =  $data->bank;
        $recipient->defaultBankAccount->branchNumber = $data->branchNumber;
        $recipient->defaultBankAccount->branchCheckDigit = $data->branchCheckDigit;
        $recipient->defaultBankAccount->accountNumber = $data->accountNumber;
        $recipient->defaultBankAccount->accountCheckDigit = $data->accountCheckDigit;
        $recipient->defaultBankAccount->type = "checking";
        $recipient->transferSettings = new CreateTransferSettingsRequest();
        $recipient->transferSettings->transferEnabled = false;
        $recipient->transferSettings->transferInterval = "Daily";
        $recipient->transferSettings->transferDay = "0";
    
        $resultRecipient = $this->recipientsController->createRecipient($recipient);
        
        //Update automatic transfer settings
        $automaticAntecipationSetting = new UpdateAutomaticAnticipationSettingsRequest();
        $automaticAntecipationSetting->enabled = true;
        $automaticAntecipationSetting->type = '1025';
        $automaticAntecipationSetting->volumePercentage = 100; //Volume disponível para antecipar (%)
        $automaticAntecipationSetting->days = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,27,28,30,31];
        $automaticAntecipationSetting->delay = '29'; //se for D+30 o delay fica em 29.
        $automaticAntecipation = $this->recipientsController->updateAutomaticAnticipationSettings($resultRecipient->id, $automaticAntecipationSetting);

        return $resultRecipient;
    }

    public function validateData($data)
    {
        $fields = ['name', 'email', 'description', 'document', 'bank', 'branchNumber', 'branchCheckDigit', 'accountNumber', 'accountCheckDigit'];
        foreach ($fields as $field) {
            if( !strlen($data->{$field}) > 0 ) {
                throw new Exception("O campo $field deve ser preenchido. Verifique os dados bancários no cadastro do cliente");
            }
        }
    }
}
