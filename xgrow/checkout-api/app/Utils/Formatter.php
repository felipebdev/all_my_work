<?php

namespace App\Utils;

use UnexpectedValueException;

/**
 * This is just a formatter class, it does not validate input (eg: verification digit)
 */
class Formatter
{
    public const CNPJ_LENGTH = 14;
    public const CPF_LENGTH = 11;
    public const CEP_LENGTH = 8;
    public const BR_PHONE_LANDLINE_LENGTH = 10;
    public const BR_PHONE_MOBILE_LENGTH = 11;
    public const PIS_PASEP_LENGTH = 11;

    public static function documentNumber(string $documentNumber): string
    {
        $stripped = self::onlyDigits($documentNumber);

        if (strlen($stripped) === self::CNPJ_LENGTH) {
            return self::cnpj($stripped);
        } elseif (strlen($stripped) === self::CPF_LENGTH) {
            return self::cpf($stripped);
        }

        throw new UnexpectedValueException('Undefined document type');
    }

    public static function onlyDigits(string $string): string
    {
        return preg_replace('/[^0-9]/', '', $string);
    }

    public static function onlyAlnum(string $string): string
    {
        return preg_replace('/[^0-9a-zA-Z]/', '', $string);
    }

    public static function cnpj(string $cnpj): string
    {
        try {
            return self::mask(self::onlyDigits($cnpj), '##.###.###/####-##');
        } catch (UnexpectedValueException $e) {
            throw new UnexpectedValueException('CNPJ should be ' . self::CNPJ_LENGTH . ' digits long');
        }
    }



    public static function cpf(string $cpf): string
    {
        try {
            return self::mask(self::onlyDigits($cpf), '###.###.###-##');
        } catch (UnexpectedValueException $e) {
            throw new UnexpectedValueException('CPF should be ' . self::CPF_LENGTH . ' digits long');
        }
    }

    public static function cep(string $cep): string
    {
        try {
            return self::mask(self::onlyDigits($cep), '#####-###');
        } catch (UnexpectedValueException $e) {
            throw new UnexpectedValueException('CEP should be ' . self::CEP_LENGTH . ' digits long');
        }
    }

    public static function brPhoneLandline(string $phoneNumber): string
    {
        try {
            return self::mask(self::onlyDigits($phoneNumber), '(##) ####-####');
        } catch (UnexpectedValueException $e) {
            throw new UnexpectedValueException('Landline number should be ' . self::BR_PHONE_LANDLINE_LENGTH . ' digits long');
        }
    }

    public static function brPhoneMobile(string $phoneNumber): string
    {
        try {
            return self::mask(self::onlyDigits($phoneNumber), '(##) #####-####');
        } catch (UnexpectedValueException $e) {
            throw new UnexpectedValueException('Mobile number should be ' . self::BR_PHONE_MOBILE_LENGTH . ' digits long');
        }
    }

    public static function pisPasep(string $pisPasep): string
    {
        try {
            return self::mask(self::onlyDigits($pisPasep), '###.#####.##.#');
        } catch (UnexpectedValueException $e) {
            throw new UnexpectedValueException('PIS/PASEP number should be ' . self::PIS_PASEP_LENGTH . ' digits long');
        }
    }

    public static function mask(string $string, string $mask, string $character = '#'): string
    {
        $count = substr_count($mask, $character);
        $strlen = strlen($string);

        if ($count !== $strlen) {
            throw new UnexpectedValueException("Mask length ({$count}) and string length ({$strlen}) mismatch");
        }

        $sprintfMask = str_replace($character, '%s', $mask);

        return vsprintf($sprintfMask, str_split($string));
    }
}
