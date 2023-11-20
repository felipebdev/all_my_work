<?php

namespace App\Utils;

class Formatter
{
    /**
     * Return only digits
     * @param $data
     * @return string
     */
    public static function onlyDigits($data)
    {
        return preg_replace('/[^0-9]/', '', $data);
    }
}