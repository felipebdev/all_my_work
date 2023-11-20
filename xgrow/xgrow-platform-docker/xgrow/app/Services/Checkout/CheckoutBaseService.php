<?php

namespace App\Services\Checkout;

use Carbon\Carbon;
use Firebase\JWT\JWT;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\Log;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Ramsey\Uuid\Uuid;

class CheckoutBaseService
{
    public static $secret = null;
    public static $expirationInMinutes = 60;
    public const ALGORITHM = 'HS256';
    private $baseUrl;

    private ?string $correlationId = null;

    public function __construct()
    {
        $this->baseUrl = env('API_URL_CHECKOUT', 'https://checkout-api.dev.xgrow.com.br/api/');
    }

    public function withCorrelationId(string $correlationId): self
    {
        $this->correlationId = $correlationId;
        return $this;
    }

    /** Token Generator for Checkout
     * @param string|null $platformId
     * @param string $userId
     * @param null|array $additionalPayloadData
     * @return string
     */
    public static function generateToken(?string $platformId, string $userId, ?array $additionalPayloadData = []): string
    {
        $minimumPayload = [
            'exp' => Carbon::now()->addMinutes(static::$expirationInMinutes)->timestamp,
            'platform_id' => $platformId,
            'user_id' => $userId,
        ];
        $payload = array_merge($minimumPayload, $additionalPayloadData ?? []);
        $secret = static::$secret ?? config('jwtplatform.jwt_web') ?? 'secret';
        $jwt = JWT::encode($payload, $secret, self::ALGORITHM);
        return $jwt;
    }

    /** Return Guzzle Http Config
     * @param string|null $platformId
     * @param string $userId
     * @param null|array $additionalPayloadData
     * @return Client
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function connectionConfig(?string $platformId, string $userId, ?array $additionalPayloadData = [])
    {
        return new Client([
            'handler' => $this->createHandlerStack(),
            'base_uri' => $this->baseUrl,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->generateToken($platformId, $userId, $additionalPayloadData),
                'X-Correlation-Id' => $this->correlationId ?? (string) Uuid::uuid4(),
            ]
        ]);
    }

    /**
     * Create a HandlerStack logging request before sending it to checkout-api
     *
     * @return \GuzzleHttp\HandlerStack
     */
    private function createHandlerStack(): HandlerStack
    {
        $stack = HandlerStack::create();

        $stack->push(function (callable $handler) {
            return function (RequestInterface $request, array $options) use ($handler) {
                Log::debug('Checkout base service request', [
                    'method' => $request->getMethod(),
                    'uri' => (string) $request->getUri(),
                    'correlation_id' => $request->getHeaderLine('X-Correlation-Id'),
                    'body' => (string) $request->getBody(),
                    //'protocol' => $request->getProtocolVersion(),
                ]);

                return $handler($request, $options);
            };
        });

        return $stack;
    }
}
