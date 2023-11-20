<?php

namespace App\Services\Contracts;

interface SendVoiceInterface
{
    /**
     * Tries to call multiple phone numbers.
     *
     * @param string $publicAudioUrl Public url to audio (mp3 format)
     * @param array $numbers List of mobile phones (digits only)
     * @return \App\Services\Objects\PhoneResponse[] List with each phone number response
     * @throw GuzzleHttp\Exception\ClientException
     */
    public function sendVoiceToNumbers(string $publicAudioUrl, array $numbers): array;
}
