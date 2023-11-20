<?php

namespace Tests\Feature\Helper;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

class MundipaggToken
{

    public const DEFAULT_CARD_NUMBER = '5368427498304369';

    public static function insufficientBalance(string $cardNumber = self::DEFAULT_CARD_NUMBER)
    {
        return self::createCardToken('651', $cardNumber);
    }

    public static function cardOk(string $cardNumber = self::DEFAULT_CARD_NUMBER)
    {
        return self::createCardToken('123', $cardNumber);
    }

    public static function randomInvalid(string $cardNumber = self::DEFAULT_CARD_NUMBER)
    {
        return self::createCardToken('6'.rand(11, 99), $cardNumber);
    }

    public static function randomValidCvv(string $cardNumber = self::DEFAULT_CARD_NUMBER)
    {
        $successor = 700;
        $predecessor = 599;
        $modulus = 1000;
        $rand = rand($successor, $predecessor + $modulus) % $modulus; // 000-599, 700-999

        $cvv = sprintf('%03d', $rand);

        return self::createCardToken($cvv, $cardNumber);
    }

    private static function createCardToken($cvv, $cardNumber): string
    {
        $publicKey = env('MUNDIPAGG_PUBLIC_KEY');

        $client = new Client();

        $createCard = $client->post("https://api.mundipagg.com/core/v1/tokens?appId={$publicKey}", [
            RequestOptions::JSON => [
                "card" => [
                    "cvv" => "$cvv",
                    "exp_month" => 3,
                    "exp_year" => 2028,
                    "holder_name" => "Testexg Financeira Me",
                    "number" => "$cardNumber",
                    "options" => [
                        "verify_card" => false
                    ]
                ],
            ],
        ]);

        $data = json_decode($createCard->getBody(), $associative = true);

        return $data['id'];
    }
}
