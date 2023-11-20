<?php


namespace App\Services\Finances\Subscriber;


use App\CreditCard;
use App\Subscriber;
use MundiAPILib\Models\GetCardResponse;

class SubscriberCreditCard
{

    public static function save(Subscriber $subscriber, GetCardResponse $card)
    {
        //Save credit card
        $creditCard = CreditCard::firstOrNew([
            'subscriber_id' => $subscriber->id,
            'card_id' => $card->id
        ]);
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
}
