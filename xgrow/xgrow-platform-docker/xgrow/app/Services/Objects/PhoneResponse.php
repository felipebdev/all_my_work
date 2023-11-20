<?php

namespace App\Services\Objects;

class PhoneResponse
{
    private $id;
    private $isSuccessful;
    private $code;
    private $message;
    private $destination;

    /**
     * SmsResponse constructor.
     *
     * @param bool $isSuccessful
     * @param int $code
     * @param string $message
     * @param string $destination
     * @param $id
     */
    public function __construct(bool $isSuccessful, int $code, string $message, string $destination, $id = null)
    {
        $this->isSuccessful = $isSuccessful;
        $this->code = $code;
        $this->message = $message;
        $this->destination = $destination;
        $this->id = $id;
    }

    /**
     * ID of the queued/sent message (null if error)
     *
     * @return mixed|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sucessfull if message was queued/sent
     *
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return $this->isSuccessful;
    }

    /**
     * Internal return code
     *
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * Success/error message
     *
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Phone number destination
     *
     * @return string
     */
    public function getDestination(): string
    {
        return $this->destination;
    }

}
