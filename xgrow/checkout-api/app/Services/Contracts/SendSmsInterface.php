<?php

namespace App\Services\Contracts;

interface SendSmsInterface
{
    /**
     * Tries to send text SMS to multiple phone numbers.
     *
     * @param string $text SMS message text
     * @param array $numbers List of mobile phones (digits only)
     * @return \App\Services\Objects\PhoneResponse[] List with each phone number response
     * @throw GuzzleHttp\Exception\ClientException
     */
    public function sendSmsToNumbers(string $text, array $numbers): array;
}
