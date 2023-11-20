<?php

namespace App\Http\Controllers\Mundipagg;

use App\CreditCard;
use App\Facades\JwtPlatformFacade;
use App\Http\Controllers\Controller;
use App\Services\Finances\Objects\CreditCardInfo;
use App\Services\Finances\Payment\CreditCardManagement;
use App\Subscriber;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use MundiAPILib\APIException;
use stdClass;

class CreditCardController extends Controller
{
    /**
     * @var \App\Services\Finances\Payment\CreditCardManagement
     */
    private $creditCardManagement;

    public function __construct(CreditCardManagement $creditCardManagement)
    {
        $this->creditCardManagement = $creditCardManagement;
    }

    /**
     * List subscriber's credit cards registered
     *
     * @return \Illuminate\Http\JsonResponse
     * @api
     */
    public function listCreditCards($subscriber_id = null)
    {
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
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @api
     */
    public function getCreditCard($id, $subscriber_id = null)
    {
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
    public function getSubscriberCreditCard($subscriber_id, $id)
    {
        return response()->json(CreditCard::where('subscriber_id', '=', $subscriber_id)->where('id', '=', $id)->get());
    }

    /**
     * Store new credit card using API
     *
     * @param  Request  $request
     * @api
     */
    public function storeCreditCard(Request $request)
    {
        $creditCardInfo = CreditCardInfo::fromCcInfo($request->all());

        $subscriber = JwtPlatformFacade::getSubscriber();
        try {
            $creditCard = $this->creditCardManagement->saveCreditCard($subscriber, $creditCardInfo);
        } catch (APIException $e) {
            $response = new stdClass();
            $response->message = $e->getMessage();
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
     * @param $credit_card_id
     * @return \Illuminate\Http\JsonResponse
     * @api
     */
    public function changeDefaultCreditCard($credit_card_id)
    {
        $subscriber = JwtPlatformFacade::getSubscriber();
        try {
            $creditCard = CreditCard::where('id', '=', $credit_card_id)
                ->where('subscriber_id', '=', $subscriber->id)
                ->firstOrFail();
            $subscriber->credit_card_id = $creditCard->id;
            $return = $subscriber->save();
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 400);
        }
        return response()->json(null, 204);
    }

    /**
     * Change default subscriber's credit card
     * @param $credit_card_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeDefaultSubscriberCreditCard($subscriber_id, $credit_card_id)
    {
        try {
            $subscriber = Subscriber::findOrFail($subscriber_id);
            $creditCard = CreditCard::where('id', '=', $credit_card_id)
                ->where('subscriber_id', '=', $subscriber->id)
                ->firstOrFail();
            $subscriber->credit_card_id = $creditCard->id;
            $return = $subscriber->save();
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 400);
        }
        return response()->json($return);
    }

    /**
     * Delete subscriber's credit card using API
     *
     * @param $creditCardId
     * @return \Illuminate\Http\JsonResponse
     * @api
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

            $this->creditCardManagement->deleteCreditCard($subscriber->customer_id, $creditCard->card_id);

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
    public function deleteSubscriberCreditCard($subscriber_id, $credit_card_id)
    {
        try {
            $subscriber = Subscriber::findOrFail($subscriber_id);
            $creditCard = CreditCard::where('id', '=', $credit_card_id)
                ->where('subscriber_id', '=', $subscriber->id)
                ->firstOrFail();

            $this->creditCardManagement->deleteCreditCard($subscriber->customer_id, $creditCard->card_id);

            $creditCard->delete();
        } catch (Exception $e) {
            if ($e->getMessage() === "Card not found.") {
                $creditCard->delete();
                return response()->json(true, 200);
            }

            return response()->json($e->getMessage(), 400);
        }
        return response()->json(true);
    }
}
