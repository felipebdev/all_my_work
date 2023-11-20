<?php

namespace App\Services\ReportAPI;

use Carbon\Carbon;
use DomainException;
use Firebase\JWT\JWT;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Config;

class ReportBaseService
{
    private static int $expirationInMinutes = 60;
    private $baseUrl;


    public function __construct()
    {
        $this->baseUrl = Config::get('jwt.report.api_url');
    }

    /** Token Generator for Report
     * @param null|array $additionalPayloadData
     * @return string
     * @throws DomainException
     */
    public static function generateToken(?array $additionalPayloadData = []): string
    {
        $minimumPayload = [
            'exp' => Carbon::now()->addMinutes(static::$expirationInMinutes)->timestamp,
            'sub' => str_random(32)
        ];
        $payload = array_merge($minimumPayload, $additionalPayloadData ?? []);
        $secret = Config::get('jwt.report.api_key');
        return JWT::encode($payload, $secret, 'HS256');
    }

    /** Return Guzzle Http Config
     * @param null|array $additionalPayloadData
     * @return Client
     */
    public function connectionConfig(?array $additionalPayloadData = []): Client
    {
        return new Client([
            'base_uri' => $this->baseUrl,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->generateToken($additionalPayloadData),
                'X-Register-Sql-Logs' => request()->header('X-Register-Sql-Logs') ?? '0',
            ]
        ]);
    }
}
