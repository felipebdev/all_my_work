<?php

namespace App\Services\Report;

use Carbon\Carbon;
use DomainException;
use Firebase\JWT\JWT;
use GuzzleHttp\BodySummarizer;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;

class ReportBaseService
{
    public static $secret = null;
    public static int $expirationInMinutes = 60;
    public const ALGORITHM = 'HS256';
    private $baseUrl;


    public function __construct()
    {
        $this->baseUrl = env('REPORT_API_URL', 'https://reports-api.mindpro.com.br/api/');
    }

    /** Token Generator for Checkout
     * @param string $platformId
     * @param string $userId
     * @param null|array $additionalPayloadData
     * @return string
     * @throws DomainException
     */
    public static function generateToken(string $platformId, string $userId, ?array $additionalPayloadData = []): string
    {
        $minimumPayload = [
            'exp' => Carbon::now()->addMinutes(static::$expirationInMinutes)->timestamp,
            'platform_id' => $platformId,
            'user_id' => $userId,
            'sub' => str_random(32)
        ];
        $payload = array_merge($minimumPayload, $additionalPayloadData ?? []);
        $secret = static::$secret ?? config('jwtplatform.jwt_report_api_key') ?? 'secret';
        return JWT::encode($payload, $secret, self::ALGORITHM);
    }

    /** Return Guzzle Http Config
     * @param string $platformId
     * @param string $userId
     * @param null|array $additionalPayloadData
     * @return Client
     */
    public function connectionConfig(string $platformId, string $userId, ?array $additionalPayloadData = []): Client
    {
        $version = request()->query('version')?? '';
        $url = $this->baseUrl.$version;

        // Create a new HandlerStack to allow more context on HTTP error message
        $stack = HandlerStack::create();
        $stack->remove('http_errors'); // remove default (not sure if really required)
        $stack->push(Middleware::httpErrors(new BodySummarizer(1000)), 'http_errors'); // add new

        return new Client([
            'handler' => $stack,
            'base_uri' => $url,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->generateToken($platformId, $userId, $additionalPayloadData),
                'X-Register-Sql-Logs' => request()->header('X-Register-Sql-Logs') ?? '0',
            ]
        ]);
    }
}
