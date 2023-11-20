<?php

namespace App\Repositories\Banks;

class Banks
{

    public static function getBankList(): array
    {
        $json = file_get_contents(base_path('resources/json/banks.json'));
        $bankList = json_decode($json);

        usort($bankList, function ($a, $b) {
            return strcmp($a->code, $b->code);
        });

        return $bankList;
    }

    public static function getBankNameByCode(?string $code): string
    {
        $banks = collect(self::getBankList());

        $bank = $banks->firstWhere('code', $code);

        if (!$bank) {
            return '';
        }

        return $bank->bank;
    }
}
