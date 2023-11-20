<?php

namespace App\Services\Mundipagg;

use Auth;
use MundiAPILib\Models\CreatePhoneRequest;
use MundiAPILib\Models\CreatePhonesRequest;
use MundiAPILib\Models\CreateAddressRequest;
use MundiAPILib\Models\CreateCustomerRequest;
use App\Integration;
use App\Services\MundipaggService;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use App\Constants;

class ClientService extends Controller
{

    private $platformId;
    private $customerController;

    public function __construct($platform_id)
    {
        $mundipaggService = new MundipaggService($platform_id);
        $client = $mundipaggService->getClient();
        $this->customerController = $client->getCustomers();

        $this->platformId = $platform_id;
    }

    public function store($subscriber)
    {

        $request = new CreateCustomerRequest();
        $request->name = $subscriber->name;
        $request->email = $subscriber->email;
        $request->type = ($subscriber->type === 'natural_person') ? 'individual' : 'company';
        $request->document = $this->prepareDocumentNumber($subscriber->document_number);
        $request->code = $subscriber->id;

        $request->address = new CreateAddressRequest();
        $request->address->line1 = $subscriber->address_number.','.$subscriber->address_street.','.$subscriber->address_district;
        $request->address->line2 = $subscriber->address_comp;
        $request->address->zipCode = $subscriber->address_zipcode;
        $request->address->city = $subscriber->address_city;
        $request->address->state = $subscriber->address_state;
        $request->address->country = "BR";

        $homePhone = $this->preparePhoneNumber($subscriber->home_phone);
        $celPhone = $this->preparePhoneNumber($subscriber->cel_phone);

        $request->phones = new CreatePhonesRequest();
        $request->phones->homePhone = new CreatePhoneRequest();
        $request->phones->homePhone->areaCode = "00";
        $request->phones->homePhone->countryCode = "55";
        $request->phones->homePhone->number = $homePhone;
        $request->phones->mobilePhone = new CreatePhoneRequest();
        $request->phones->mobilePhone->areaCode = "00";
        $request->phones->mobilePhone->countryCode = "55";
        $request->phones->mobilePhone->number = $celPhone;


        try {
            $result = $this->customerController->createCustomer($request);
            $return  = json_encode($result, JSON_PRETTY_PRINT);

            $subscriber->integratable()->delete();
            $integration = Integration::where('id_integration', '=', Constants::CONSTANT_INTEGRATION_MUNDIPAGG)->where('platform_id', $this->platformId)->first();
            $subscriber->integratable()->create(['integration_id' => $integration->id, 'integration_type_id' => $result->id]);

            return ['status' => 'success', 'data' => $return];

        } catch (\Exception $e) {
            return ['status' => 'error', 'data' => $e];
        }
    }

    private function preparePhoneNumber($number)
    {
        return ($number) ? str_replace(' ', '', str_replace('-', '', str_replace(')', '', str_replace('(', '', $number)))) : "000000000";
    }

    private function prepareDocumentNumber($number)
    {
        return str_replace('/', '', str_replace('-', '', str_replace('.', '', $number)));
    }
}
