<?php

namespace App\Http\Controllers\Mundipagg;

use App\CreditCard;
use App\Facades\JwtPlatformFacade;
use App\Http\Controllers\Controller;
use App\Services\MundipaggService;
use App\Subscriber;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use MundiAPILib\APIException;
use MundiAPILib\Models\CreateCardRequest;
use MundiAPILib\Models\GetCardResponse;

class CreditCardController extends Controller
{
    public $mundipaggService;

    public function __construct(MundipaggService $mundipaggService = null)
    {
        if($mundipaggService) {
            $this->mundipaggService = $mundipaggService;
        }
    }

    public function saveCreditCard(Subscriber $subscriber, Request $data, $tokenId = null) {
        $creditCardRequest = $this->getCardRequest($data, $tokenId);
        $card = $this->mundipaggService->getClient()->getCustomers()->createCard($subscriber->customer_id, $creditCardRequest);
        return self::save($subscriber, $card);
    }

    public static function save(Subscriber $subscriber, GetCardResponse $card) {
        //Save credit card
        $creditCard = CreditCard::firstOrNew(
            ['subscriber_id' => $subscriber->id,
                'card_id' => $card->id]
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

    public function saveCreditCards(Subscriber $subscriber, Request $request) {
        $cards = array();
        $ccinfo = $this->getCreditCardData($request);
        if( count($ccinfo) > 0 ) {
            foreach($ccinfo as $cod => $info ) {
                $cards[] = $this->saveCreditCard($subscriber, $request, $info['token']);
            }
        }
        return $cards;
    }

    public function getCreditCardData(Request $request) {
        if( empty($request->cc_info) ) {
            throw new \Exception("Dados dos cartões inválidos");
        }
        return $request->cc_info;
    }

    /**
     * Create CreateCardRequest object
     * @param Request $data
     * @param null $tokenId
     * @return CreateCardRequest
     */
    private function getCardRequest(Request $data, $tokenId = null) {
        $creditCardRequest = new CreateCardRequest();
        if( strlen($tokenId) > 0 ) {
            $creditCardRequest->token = $tokenId;
        }
        else
        {
            $creditCardRequest->number = (string) $data->number;
            $creditCardRequest->holderName = (string) $data->holder_name;
            $creditCardRequest->holderDocument = (string) $data->holder_document;
            $creditCardRequest->expMonth = (string) $data->exp_month;
            $creditCardRequest->expYear = (string) $data->exp_year;
            $creditCardRequest->brand = (string) $data->brand; //Elo, Mastercard, Visa, Amex, JCB, Aura, Hipercard, Diners ou Discover
            $creditCardRequest->cvv = (string) $data->cvv;
        }
        if( strlen($data->address_zipcode) > 0 ) {
            $creditCardRequest->billingAddress = AddressController::getAddress($data);
        }
        return $creditCardRequest;
    }

    /**
     * List subscriber's credit cards registered
     *
     * @api
     * @return \Illuminate\Http\JsonResponse
     */
    public function listCreditCards($subscriber_id = null) {
        $userId = $subscriber_id ?? JwtPlatformFacade::getSubscriber()->id;

        $subscriber = Subscriber::findOrFail($userId);
        $defaultCreditCard = $subscriber->credit_card_id;

        $creditCards = CreditCard::where('subscriber_id', '=', $subscriber->id)->get();

        $cardsInfo = $creditCards->map(function ($creditCard) use ($defaultCreditCard) {
            $creditCard['is_default'] = $creditCard->id === $defaultCreditCard;
            return $creditCard;
        });

        return response()->json($cardsInfo);
    }

    /**
     * Get credit card data
     *
     * @api
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCreditCard($id, $subscriber_id = null) {
        $userId = $subscriber_id ?? JwtPlatformFacade::getSubscriber()->id;

        try {
            $subscriber = Subscriber::findOrFail($userId);
            $defaultCreditCard = $subscriber->credit_card_id;

            $creditCard = CreditCard::where('subscriber_id', '=', $subscriber->id)
                ->where('id', '=', $id)
                ->firstOrFail();

            $creditCard['is_default'] = $creditCard->id === $defaultCreditCard;

            return response()->json($creditCard);
        } catch (ModelNotFoundException $e) {
            return response()->json('Card from subscriber not found', 404);
        }
    }
    /**
     * Get credit card data from subscriber
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSubscriberCreditCard($subscriber_id, $id) {
        return response()->json(CreditCard::where('subscriber_id', '=', $subscriber_id)->where('id', '=', $id)->get());
    }

    /**
     * Store new credit card using API
     *
     * @api
     * @param Request $request
     */
    public function storeCreditCard(Request $request)
    {
        $subscriber = JwtPlatformFacade::getSubscriber();
        try {
            $this->mundipaggService = new MundipaggService(JwtPlatformFacade::getPlatformId());
            $creditCard = $this->saveCreditCard($subscriber, $request);
        } catch (APIException $e) {
            $response = new \stdClass();
            $response->message = $e->getMessage();
            $response->errors = $e->errors;
            return response()->json($response, 400);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 400);
        }

        return response()->json($creditCard);
    }


    /**
     * Store new credit card for subscriber
     * @param Request $request
     */
    public function storeSubscriberCreditCard($subscriber_id, Request $request)
    {
        try {
            $subscriber = Subscriber::findOrFail($subscriber_id);
            $this->mundipaggService = new MundipaggService($subscriber->platform_id);

            if (!isset($subscriber->customer_id)) {
                return response()->json("Unable to register without a customer id", 400);
            }

            $creditCard = $this->saveCreditCard($subscriber, $request);
        } catch (APIException $e) {
            $response = new \stdClass();
            $response->message = $e->getMessage();
            if (isset($e->errors))
                $response->errors = $e->errors;
            return response()->json($response, 400);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 400);
        }

        return response()->json($creditCard);
    }

    /**
     * Change default subscriber's credit card using API
     *
     * @api
     * @param $credit_card_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeDefaultCreditCard($credit_card_id) {
        $subscriber = JwtPlatformFacade::getSubscriber();
        try {
            $creditCard = CreditCard::where('id', '=', $credit_card_id)
                ->where('subscriber_id', '=', $subscriber->id)
                ->firstOrFail();
            $subscriber->credit_card_id = $creditCard->id;
            $return = $subscriber->save();
        }
        catch (Exception $e) {
            return response()->json($e->getMessage(), 400);
        }
        return response()->json(null, 204);
    }
    /**
     * Change default subscriber's credit card
     * @param $credit_card_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeDefaultSubscriberCreditCard($subscriber_id, $credit_card_id) {
        try {
            $subscriber = Subscriber::findOrFail($subscriber_id);
            $creditCard = CreditCard::where('id', '=', $credit_card_id)->where('subscriber_id', '=', $subscriber->id)->firstOrFail();
            $subscriber->credit_card_id = $creditCard->id;
            $return = $subscriber->save();
        }
        catch (Exception $e) {
            return response()->json($e->getMessage(), 400);
        }
        return response()->json($return);
    }

    /**
     * Delete subscriber's credit card using API
     *
     * @api
     * @param $creditCardId
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteCreditCard($creditCardId)
    {
        $subscriber = JwtPlatformFacade::getSubscriber();
        try {
            $creditCard = CreditCard::where('id', '=', $creditCardId)
                ->where('subscriber_id', '=', $subscriber->id)
                ->firstOrFail();

            $isDefaultCreditCard = $creditCard->id === $subscriber->credit_card_id;

            if ($isDefaultCreditCard) {
                return response()->json('Não é possível excluir seu cartão principal', 409);
            }

            $this->mundipaggService = new MundipaggService($subscriber->platform_id);
            $card = $this->mundipaggService->getClient()->getCustomers()->deleteCard($subscriber->customer_id, $creditCard->card_id);
            $creditCard->delete();

            return response()->json(null, 204);
        } catch (Exception $e) {
            if ($e->getMessage() === "Card not found.") {
                $creditCard->delete();
                return response()->json(null, 204);
            }

            return response()->json($e->getMessage(), 400);
        }

    }

    /**
     * Delete subscriber's credit card
     *
     * @param $credit_card_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteSubscriberCreditCard($subscriber_id, $credit_card_id) {
        try {
            $subscriber = Subscriber::findOrFail($subscriber_id);
            $creditCard = CreditCard::where('id', '=', $credit_card_id)->where('subscriber_id', '=', $subscriber->id)->firstOrFail();
            $this->mundipaggService = new MundipaggService($subscriber->platform_id);
            $card = $this->mundipaggService->getClient()->getCustomers()->deleteCard($subscriber->customer_id, $creditCard->card_id);
            $creditCard->delete();
        }
        catch (Exception $e) {
            if ($e->getMessage() === "Card not found.") {
                $creditCard->delete();
                return response()->json(true, 200);
            }

            return response()->json($e->getMessage(), 400);
        }
        return response()->json(true);
    }
}
