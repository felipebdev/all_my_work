<?php

namespace Modules\Integration\Providers;

use Modules\Integration\Models\Integration;

class BaseProvider
{
    /**
     * @var Modules\Integration\Enums\TypeEnum
     */
    protected $type;

    /**
     * @var string
     */
    protected $apiKey;

    /**
     * @var string
     */
    protected $apiAccount;

    /**
     * @var string
     */
    protected $apiWebhook;

    /**
     * @var string
     */
    protected $apiSecret;

    /**
     * @var object
     */
    protected $metadata;

    public function build(Integration $integration)
    {
        $this->type = $integration->type;
        $this->apiKey = $integration->api_key;
        $this->apiAccount = $integration->api_account;
        $this->apiWebhook = $integration->api_webhook;
        $this->apiSecret = $integration->api_secret;
        $this->metadata = $integration->metadata;
        return $this;
    }

    /**
     * @return Modules\Integration\Enums\TypeEnum
     */ 
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */ 
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @return string
     */ 
    public function getApiAccount()
    {
        return $this->apiAccount;
    }

    /**
     * @return string
     */ 
    public function getApiWebhook()
    {
        return $this->apiWebhook;
    }

    /**
     * @return string
     */ 
    public function getApiSecret()
    {
        return $this->apiSecret;
    }

    /**
     * @return object
     */ 
    public function getMetadata()
    {
        return $this->metadata;
    }
}
