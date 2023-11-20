<?php

namespace App\Services\Finances\Payment;

use App\CreditCard;
use App\Services\Finances\Customer\Address;
use App\Services\Finances\Objects\AddressInfo;
use App\Services\Finances\Objects\CreditCardInfo;
use App\Services\Finances\Objects\OrderInfo;
use App\Services\MundipaggService;
use App\Subscriber;
use Illuminate\Http\Request;
use MundiAPILib\Controllers\CustomersController;
use MundiAPILib\Models\CreateCardRequest;
use MundiAPILib\Models\GetCardResponse;

class CreditCardManagement
{

    protected MundipaggService $mundipaggService;

    public function __construct()
    {
        $this->mundipaggService = new MundipaggService();
    }

    /**
     * Save a single credit card on Gateway
     *
     * @param  \App\Subscriber  $subscriber
     * @param  \App\Services\Finances\Objects\CreditCardInfo  $creditCardInfo
     * @param  \App\Services\Finances\Objects\AddressInfo|null  $addressInfo
     * @return \App\CreditCard
     * @throws \MundiAPILib\APIException
     * @throws \MundiAPILib\Exceptions\ErrorException
     */
    public function saveCreditCard(
        Subscriber $subscriber,
        CreditCardInfo $creditCardInfo,
        ?AddressInfo $addressInfo = null
    ): CreditCard {
        $creditCardRequest = $this->getCardRequest($creditCardInfo, $addressInfo);
        $card = $this->customers()->createCard($subscriber->customer_id, $creditCardRequest);
        return $this->save($subscriber, $card);
    }

    /**
     * Save multiple credit cards on Gateway
     *
     * @param  \App\Services\Finances\Objects\OrderInfo  $orderInfo
     * @param  \App\Subscriber  $subscriber
     * @return array
     * @throws \MundiAPILib\APIException
     */
    public function saveCreditCards(OrderInfo $orderInfo, Subscriber $subscriber)
    {
        $cards = [];
        $ccinfo = $orderInfo->validateCcInfo()->getCcInfo();
        if (count($ccinfo) > 0) {
            foreach ($ccinfo as $cod => $info) {
                $creditCardInfo = CreditCardInfo::fromCcInfo($info);
                $cards[] = $this->saveCreditCard($subscriber, $creditCardInfo, $orderInfo->getAddressInfo());
            }
        }
        return $cards;
    }

    /**
     * Delete card on Gateway
     *
     * @param $customerId
     * @param $cardId
     * @throws \MundiAPILib\APIException
     */
    public function deleteCreditCard($customerId, $cardId)
    {
        $card = $this->customers()->deleteCard($customerId, $cardId);
    }

    protected function customers(): CustomersController
    {
        return $this->mundipaggService->getClient()->getCustomers();
    }

    /**
     * @param  \App\Subscriber  $subscriber
     * @param  \MundiAPILib\Models\GetCardResponse  $card
     * @return \App\CreditCard
     */
    protected function save(Subscriber $subscriber, GetCardResponse $card): CreditCard
    {
        //Save credit card
        $creditCard = CreditCard::firstOrNew(
            [
                'subscriber_id' => $subscriber->id,
                'card_id' => $card->id
            ]
        );
        $creditCard->brand = $card->brand;
        $creditCard->last_four_digits = $card->lastFourDigits;
        $creditCard->holder_name = $card->holderName;
        $creditCard->exp_month = $card->expMonth;
        $creditCard->exp_year = $card->expYear;
        $creditCard->save();

        //Change subscriber's default credit card
        $subscriber->credit_card_id = $creditCard->id;
        $subscriber->save();

        return $creditCard;
    }

    /**
     * Create CreateCardRequest object
     *
     * @param  Request  $creditCardInfo
     * @param  null  $tokenId
     * @param  \App\Services\Finances\Objects\AddressInfo|null  $addressInfo
     * @return CreateCardRequest
     */
    protected function getCardRequest(CreditCardInfo $creditCardInfo, AddressInfo $addressInfo = null)
    {
        $creditCardRequest = new CreateCardRequest();

        $tokenId = $creditCardInfo->getTokenId();
        if ($tokenId) {
            $creditCardRequest->token = $tokenId;
        } else {
            $creditCardRequest->number = $creditCardInfo->getNumber();
            $creditCardRequest->holderName = $creditCardInfo->getHolderName();
            $creditCardRequest->holderDocument = $creditCardInfo->getHolderDocument();
            $creditCardRequest->expMonth = $creditCardInfo->getExpMonth();
            $creditCardRequest->expYear = $creditCardInfo->getExpYear();
            $creditCardRequest->brand = $creditCardInfo->getBrand(); //Elo, Mastercard, Visa, Amex, JCB, Aura, Hipercard, Diners ou Discover
            $creditCardRequest->cvv = $creditCardInfo->getCvv();
        }

        if ($addressInfo) {
            $creditCardRequest->billingAddress = Address::getAddress($addressInfo);
        }
        return $creditCardRequest;
    }
}
