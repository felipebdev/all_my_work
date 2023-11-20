<?php

namespace App\Services\Integrations;

use App\Exceptions\BadConfigurationException;
use App\Integration;
use App\Services\Contracts\FacebookPixelServiceInterface;
use App\Services\Contracts\PlatformableInterface;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
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

    /**
     * FacebookPixelService constructor.
     *
     * @param  string|null  $platformId
     * @throws \App\Exceptions\BadConfigurationException
     */
    public function __construct(?string $platformId)
    {
        if ($platformId) {
            $this->setPlatformId($platformId);
        }
    }

    /**
     * @param  string  $platformId
     * @return mixed|void
     * @throws \App\Exceptions\BadConfigurationException
     */
    public function setPlatformId(string $platformId)
    {
        $this->platformId = $platformId;
        if ($platformId) {
            $this->loadFacebookPixelInfo($this->platformId);
        }
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
            function () use ($platformId) {
                return Integration::where('platform_id', $platformId)
                    ->where('id_integration', 'FACEBOOKPIXEL')
                    ->where('flag_enable', 1)
                    ->first();
            });

        if (!$facebookPixel) {
            throw new BadConfigurationException('Facebook Pixel integration not found for this platform');
        }

        $pixel = $this->getAndValidatePixelData($facebookPixel);

        $this->setAuth($pixel->id, $pixel->token);
        $this->setTestEventCode($pixel->test_event_code);

        return $this;
    }

    /**
     * @param  \App\Integration  $facebookPixel
     * @return \stdClass
     * @throws \App\Exceptions\BadConfigurationException
     */
    private function getAndValidatePixelData(Integration $facebookPixel): stdClass
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
