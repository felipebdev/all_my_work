<?php

namespace App\Services\Mundipagg;

use MundiAPILib\Models\CreateCardOptionsRequest;
use MundiAPILib\Models\CreateAddressRequest;
use MundiAPILib\Models\CreateCardRequest;
use MundiAPILib\Models\CreateCustomerRequest;
use App\Services\MundipaggService;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;

class CardService extends Controller
{
    private $customerController;
    private $platformId;

    public function __construct($platform_id)
    {
        $mundipaggService = new MundipaggService($platform_id);
        $client = $mundipaggService->getClient();
        $this->customerController = $client->getCustomers();
        $this->platformId = $platform_id;
    }

    public function store($data)
    {
        $customer = new CreateCustomerRequest();
        $customer->name = $data->subscriberName;
        $customer->email = $data->subscriberEmail;

        $request = new CreateCardRequest();

        $request->number = $this->prepareCardNumber($data->number);
        $request->holderName = $data->holderName;
        $request->holderDocument = $this->prepareHolderDocument($data->holderDocument);
        $request->expMonth = $data->expMonth;
        $request->expYear = $data->expYear;
        $request->cvv = $data->cvv;

        // Billing Address;
        $request->billingAddress = new CreateAddressRequest();
        $request->billingAddress->line1 = $data->line1;
        $request->billingAddress->line2 = $data->line2;
        $request->billingAddress->zipCode = $data->zipCode;
        $request->billingAddress->city = $data->city;
        $request->billingAddress->state = $data->state;
        $request->billingAddress->country = "BR";

        // Card Options: Verify OneDollarAuth;
        $request->options = new CreateCardOptionsRequest();
        $request->options->verifyCard = true;

        try {
            $createdCustomer = $this->customerController->createCustomer($customer);
            $result = $this->customerController->createCard($createdCustomer->id, $request);
            $return  = json_encode($result, JSON_PRETTY_PRINT);

            return ['status' => 'success', 'data' => $return];

        } catch (\Exception $e) {
            return ['status' => 'error', 'data' => $e];
        }
    }

    private function prepareCardNumber($number)
    {
        return str_replace(' ', '', $number);
    }

    private function prepareHolderDocument($number)
    {
        return str_replace('/', '', str_replace('-', '', str_replace('.', '', $number)));
    }
}
