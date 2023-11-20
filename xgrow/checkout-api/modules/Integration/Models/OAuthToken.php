<?php

namespace Modules\Integration\Models;

class OAuthToken
{
    /**
     * @var string
     */
    private $accessToken;

    /**
     * @var string
     */
    private $refreshToken;

    /**
     * @var int
     */
    private $expiresIn;

    public function __construct(
        string $accessToken, 
        string $refreshToken,
        int $expiresIn
    ) {
        $this->accessToken = $accessToken;
        $this->refreshToken = $refreshToken;
        $this->expiresIn = $expiresIn;
    }

    /**
     * @return string
     */
    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    /**
     * @return string
     */
    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    /**
     * @return int
     */ 
    public function getExpiresIn()
    {
        return $this->expiresIn;
    }
}
