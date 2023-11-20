<?php

namespace App\Services\MyData;

use App\Client;
use App\Http\Requests\MyData\IdentityRequest;
use App\Http\Traits\CustomResponseTrait;
use App\Platform;
use App\Repositories\DocumentValidation\ValidateDocumentsRepository;
use App\Repositories\MyData\MyDataRepository;
use App\Services\Checkout\BankAccountService;
use App\Services\Checkout\RecipientsService;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MyDataService
{
    use CustomResponseTrait;
    private ValidateDocumentsRepository $validateDocumentsRepository;
    private BankAccountService $bankAccountService;
    private MyDataRepository $myDataRepository;
    private RecipientsService $recipientService;

    private $recipientStatusDenied = [
        'refused',
        'suspended',
        'blocked',
        'inactive',
    ];

    public function __construct(
        ValidateDocumentsRepository $validateDocumentsRepository,
        BankAccountService $bankAccountService,
        MyDataRepository $myDataRepository,
        RecipientsService $recipientService
    )
    {
        $this->validateDocumentsRepository = $validateDocumentsRepository;
        $this->bankAccountService = $bankAccountService;
        $this->myDataRepository = $myDataRepository;
        $this->recipientService = $recipientService;
    }

    public function storeIdentity(IdentityRequest $request): array
    {
        $image = $_FILES['file'];
        $directory = "";

        $documentValidation = (object) $this->validateDocumentsRepository->validateAndUpload($image, $directory, $request->document);

        $userData = (object) $this->getUserData();

        $responseBankAccount = $this->bankAccountService->createBankAccount($userData->platformId, $request);

        //Create platform client recipient
        if (is_null($userData->platform->recipient_id)
            || in_array($userData->platform->recipient_status, $this->recipientStatusDenied)
        ) {
            $this->recipientService->createProducerRecipient($userData->platformId, 'client');
        }

        //Update client data
        $this->myDataRepository->updateIdentity($request, $documentValidation->imageUrl);

        return [
            'error' => false,
            'message' => 'Identidade cadastrada com sucesso.',
            'response' => $responseBankAccount['response'],
            'code' => 200
        ];
    }

    private function getUserData()
    {
        $platformId = null;
        $client = Client::where('email', Auth::user()->email)->first();
        if ($client) {
            $platform = Platform::where('customer_id', $client->id)->first();
            $platformId = $platform->id;
        }
        if (!$platformId) {
            throw new Exception("Cliente não possui plataforma", 422);
        }
        return compact('client', 'platformId', 'platform');
    }

}
