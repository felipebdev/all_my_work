<?php

namespace App\Services\Integrations;

use App\Exceptions\BadConfigurationException;
use App\Integration as LegacyIntegration;
use App\Services\Contracts\FacebookPixelServiceInterface;
use App\Services\Contracts\PlatformableInterface;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Modules\Integration\Models\Integration;
use Psr\Http\Message\ResponseInterface;
use stdClass;

/**
 * Class FacebookPixelService
 *
 * @package App\Services\Integrations
 */
class FacebookPixelService implements FacebookPixelServiceInterface, PlatformableInterface
{
    private $platformId;
    private $id;
    private $token;
    private $testEventCode;

    private FacebookPixelRepository $repository;

    /**
     * FacebookPixelService constructor.
     *
     * @param  string|null  $platformId
     * @throws \App\Exceptions\BadConfigurationException
     */
    public function __construct(?string $platformId = null)
    {
        $this->repository = app()->make(FacebookPixelRepository::class);

        if ($platformId) {
            $this->setPlatformId($platformId);
        }
    }

    /**
     * @param  string  $platformId
     * @return mixed|void
     * @throws \App\Exceptions\BadConfigurationException
     */
    public function setPlatformId(string $platformId): self
    {
        $this->platformId = $platformId;
        if ($platformId) {
            $this->loadFacebookPixelInfo($this->platformId);
        }
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPlatformId(): ?string
    {
        return $this->platformId;
    }

    /**
     * @param  string  $id
     * @param  string  $token
     * @return $this
     */
    protected function setAuth(string $id, string $token): self
    {
        $this->id = $id;
        $this->token = $token;
        return $this;
    }

    /**
     * @param  string|null  $testEventCode
     * @return $this
     */
    protected function setTestEventCode(?string $testEventCode): self
    {
        $this->testEventCode = $testEventCode;
        return $this;
    }

    /**
     * Send raw data to Facebook
     *
     * @param  array  $data
     * @param  string|null  $testEventCode
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendSingleRawData(array $data, ?string $testEventCode = null): ResponseInterface
    {
        $params = [
            'data' => [$data],
            'access_token' => $this->token,
        ];

        if (!is_null($testEventCode) && $testEventCode == $this->testEventCode) {
            $params['test_event_code'] = $testEventCode;
        }

        $client = new Client();
        $response = $client->post("https://graph.facebook.com/v11.0/{$this->id}/events", [
            'form_params' => $params,
        ]);
        return $response;
    }

    /**
     * @param  string  $platformId
     * @return $this
     * @throws \App\Exceptions\BadConfigurationException
     */
    private function loadFacebookPixelInfo(string $platformId)
    {
        $facebookPixel = Cache::store('redis')->remember("facebook-pixel:{$platformId}", 10,
            fn() => $this->repository->loadFacebookPixelFromDatabase($platformId)
        );

        if (!$facebookPixel) {
            throw new BadConfigurationException('Facebook Pixel integration not found for this platform');
        }

        if ($facebookPixel->type === 'facebookpixel') {
            $pixel = $this->getAndValidatePixelData($facebookPixel);
        } else {
            $pixel = $this->getAndValidateLegacyPixelData($facebookPixel);
        }

        $this->setAuth($pixel->id, $pixel->token);
        $this->setTestEventCode($pixel->test_event_code);

        return $this;
    }

    /**
     * Validate Pixel data from `apps` table
     *
     * @param  \Modules\Integration\Models\Integration  $facebookPixel
     * @return \stdClass
     * @throws \App\Exceptions\BadConfigurationException
     */
    private function getAndValidatePixelData(Integration $facebookPixel): stdClass
    {
        if (strlen($facebookPixel->api_account) <= 0 || strlen($facebookPixel->api_key) <= 0) {
            throw new BadConfigurationException('Facebook Pixel not configured');
        }

        $result = new stdClass();
        $result->id = $facebookPixel->api_account;
        $result->token = $facebookPixel->api_key;
        $result->test_event_code = $facebookPixel->metadata['test_event_code'] ?? null;

        return $result;
    }

    /**
     * Validate Legacy Pixel data from `integrations` table
     *
     * @param  \App\Integration  $facebookPixel
     * @return \stdClass
     * @throws \App\Exceptions\BadConfigurationException
     */
    private function getAndValidateLegacyPixelData(LegacyIntegration $facebookPixel): stdClass
    {
        if (strlen($facebookPixel->source_token) <= 0) {
            throw new BadConfigurationException('Facebook Pixel not configured');
        }

        $pixel = json_decode($facebookPixel->source_token);
        if (is_null($pixel->pixel_id ?? null) || is_null($pixel->pixel_token ?? null)) {
            throw new BadConfigurationException('Facebook Pixel configured improperly');
        }

        $result = new stdClass();
        $result->id = $pixel->pixel_id;
        $result->token = $pixel->pixel_token;
        $result->test_event_code = $pixel->pixel_test_event_code ?? null;

        return $result;
    }
}
