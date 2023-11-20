<?php

namespace App\Services\Auth\Objects;

class ClientStatusResult
{

    /**
     * @var bool true if Client was found, false otherwise
     */
    public bool $isClient;

    /**
     * @var bool|null True if client is found and
     */
    public ?bool $isOwner;

    /**
     *
     *
     * @var bool
     */
    public bool $clientApproved;

    public ?string $recipientStatusMessage;

    public bool $mustVerify;

    protected function __construct(
        bool $isClient,
        bool $clientApproved,
        ?bool $isOwner,
        ?string $recipientStatusMessage
    ) {
        $this->isClient = $isClient;
        $this->clientApproved = $clientApproved;
        $this->isOwner = $isOwner;
        $this->recipientStatusMessage = $recipientStatusMessage;
        $this->mustVerify = $isClient && !$clientApproved;
    }

    public static function notFound(): self
    {
        return new self(false, false, null, null);
    }

    public static function foundWithoutPlatform(bool $clientApproved)
    {
        return new self(true, $clientApproved, null, null);
    }

    public static function foundWithPlatform(
        bool $clientApproved,
        bool $isOwner,
        string $recipientStatusMessage = ''
    ): self {
        return new self(true, $clientApproved, $isOwner, $recipientStatusMessage);
    }

}
