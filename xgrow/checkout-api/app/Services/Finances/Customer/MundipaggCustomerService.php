<?php

namespace App\Services\Finances\Customer;

use App\Services\Finances\Objects\Constants;
use App\Services\Finances\Objects\OrderInfo;
use App\Services\Finances\Payment\Exceptions\FailedTransaction;
use App\Services\MundipaggService;
use App\Subscriber;
use App\Utils\Formatter;
use Illuminate\Support\Facades\Log;
use MundiAPILib\APIException;
use MundiAPILib\Models\CreateCustomerRequest;
use MundiAPILib\Models\CreatePhoneRequest;
use MundiAPILib\Models\CreatePhonesRequest;
use MundiAPILib\Models\GetCustomerResponse;

class MundipaggCustomerService
{

    const MESSAGE_INVALID_PARAMS = 'Não foi possível finalizar a compra. Verifique os campos preenchidos e tente novamente';

    protected MundipaggService $mundipaggService;

    public function __construct()
    {
        $this->mundipaggService = new MundipaggService();
    }

    public function getCustomerIdOrCreate(OrderInfo $orderInfo, Subscriber $subscriber)
    {
        if ($subscriber->customer_id) {
            return $subscriber->customer_id;
        }

        $customer = $this->saveCustomer($orderInfo, $subscriber);

        $subscriber->customer_id = $customer->id;

        return $customer->id;
    }

    public function saveCustomer(OrderInfo $orderRequest, Subscriber $subscriber): GetCustomerResponse
    {
        try {
            $response = $this->subscriberToMundipaggCustomer($orderRequest, $subscriber);
        } catch (APIException $e) {
            Log::error('EXCEPTION');
            Log::error(json_encode($e));
            Log::error('SUBSCRIBER');
            Log::error(json_encode($subscriber));
            Log::error('REQUEST');
            Log::error(json_encode($orderRequest));
            throw new FailedTransaction(self::MESSAGE_INVALID_PARAMS);
        }

        return $response;
    }

    /**
     * Convert a Subscriber (Checkout) to Customer (API)
     *
     * @param  \App\Services\Finances\Objects\OrderInfo  $orderInfo
     * @param  \App\Subscriber  $subscriber
     * @return mixed
     * @throws \MundiAPILib\APIException
     */
    protected function subscriberToMundipaggCustomer(OrderInfo $orderInfo, Subscriber $subscriber): GetCustomerResponse
    {
        $customer = new CreateCustomerRequest();
        $customer->name = $subscriber->name;

        $foreignDocumentTypes = [
            Subscriber::DOCUMENT_TYPE_PASSPORT,
            Subscriber::DOCUMENT_TYPE_OTHER_NATURAL,
            Subscriber::DOCUMENT_TYPE_OTHER_LEGAL,
            null, // no document for foreigner
        ];

        $documentType = $subscriber->document_type ?? null;

        if ($documentType == Subscriber::DOCUMENT_TYPE_CPF) {
            $customer->document = Formatter::onlyDigits($subscriber->document_number);
            $customer->document_type = Constants::MUNDIPAGG_CPF;
            $customer->type = Constants::MUNDIPAGG_INDIVIDUAL;
        } elseif ($documentType == Subscriber::DOCUMENT_TYPE_CNPJ) {
            $customer->document = Formatter::onlyDigits($subscriber->document_number);
            $customer->document_type = Constants::MUNDIPAGG_CNPJ;
            $customer->type = Constants::MUNDIPAGG_COMPANY;
        } elseif (in_array($documentType, $foreignDocumentTypes)) {
            $customer->document = Constants::MUNDIPAGG_CNPJ_FOREIGNER; // use Mundipagg CNPJ for foreigner
            $customer->type = Constants::MUNDIPAGG_COMPANY;
        }

        $customer->email = $subscriber->email;
        $customer->phones = new CreatePhonesRequest();
        $customer->phones->mobilePhone = new CreatePhoneRequest();
        $customer->phones->mobilePhone->countryCode = $subscriber->phone_country_code;
        $customer->phones->mobilePhone->areaCode = $subscriber->phone_area_code;
        $customer->phones->mobilePhone->number = $subscriber->phone_number;

        $addressInfo = $orderInfo->getAddressInfo();
        if ($addressInfo->getZipcode() > 0) {
            $customer->address = Address::getAddress($addressInfo);
        }

        //Create or update customer
        $response = $this->mundipaggService->getClient()->getCustomers()->createCustomer($customer);
        return $response;
    }
}
