<?php

namespace App\Jobs;

use App\Exceptions\BadConfigurationException;
use App\Services\Integrations\FacebookPixelService;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

class FacebookPixelJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    private $platformId;
    private $data;
    private $testEventCode;

    public function __construct(string $platformId, int $planId, array $data, ?string $testEventCode= null)
    {
        $this->platformId = $platformId;
        $this->plandId = $planId;
        $this->data = $data;
        $this->testEventCode = $testEventCode;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $platformId = $this->platformId ?? 'undefined';
        $jsonData = json_encode($this->data ?? []);

        $this->err("Processing data for {$platformId}. Data provided: {$jsonData}. Test Code: {$this->testEventCode}");

        try {
            $service = new FacebookPixelService($this->platformId);
            $response = $service->sendSingleRawData($this->data, $this->testEventCode);

            $this->err("Response for {$platformId}. Code: {$response->getStatusCode()}. Body: {$response->getBody()}");
            $this->err("Data processed successfully for {$platformId}. Data provided: {$jsonData}. Test Code: {$this->testEventCode}");
        } catch (ClientException $exception) {
            $this->handleClientException($exception);
        } catch (BadConfigurationException $exception) {
            $this->handleBadConfiguration($exception, $platformId);
        }
    }

    private function handleClientException(ClientException $exception)
    {
        $messsage = $exception->getMessage() ?? 'undefined';
        $platformId = $this->platformId ?? 'undefined';
        $jsonData = json_encode($this->data ?? []);

        $this->err("Error while processing data for {$platformId}. Error: {$messsage}. Data provided: {$jsonData}. Test Code: {$this->testEventCode}");

        if ($exception->hasResponse()) {
            $httpCode = $exception->getCode() ?? 'undefined';

            $errorResponse = $exception->getResponse();
            $jsonResponse = $errorResponse->getBody()->getContents() ?? 'undefined';

            $this->err("Error while processing {$platformId}. Code: {$httpCode}; JSON response: {$jsonResponse}");
        }
    }

    private function handleBadConfiguration(BadConfigurationException $exception, ?string $platformId = null)
    {
        $message = $exception->getMessage();
        $this->err("Bad Facebook Pixel configuration: {$platformId}. Message: {$message}");
    }

    private function err($message): void
    {
        fwrite(STDERR, $message."\n");
    }
}
