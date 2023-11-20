<?php

namespace App\Jobs;

use App\Exceptions\BadConfigurationException;
use App\Services\Integrations\FacebookPixelService;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class FacebookPixelJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    private $platformId;
    private $data;
    private $testEventCode;

    public function __construct(string $platformId, int $planId, array $data, ?string $testEventCode = null)
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
        $platformId = $this->platformId ?? null;

        Log::withContext(['platform_id' => $platformId]);
        Log::withContext(['plan_id' => $this->plandId ?? null]);
        Log::withContext(['data' => $this->data ?? null]);
        Log::withContext(['test_code' => $this->testEventCode ?? null]);

        Log::debug('Processing FacebookPixelJob');

        try {
            $service = new FacebookPixelService($platformId);
            $response = $service->sendSingleRawData($this->data, $this->testEventCode);

            Log::debug('FacebookPixel response', [
                'code' => $response->getStatusCode() ?? null,
                'body' => $response->getBody() ?? null,
                'success' => true,
            ]);
        } catch (ClientException $exception) {
            $this->handleClientException($exception);
        } catch (BadConfigurationException $exception) {
            $this->handleBadConfiguration($exception);
        }
    }

    private function handleClientException(ClientException $exception)
    {
        Log::withContext(['success' => false]);
        Log::withContext(['exception_message' => $exception->getMessage()]);
        Log::withContext(['exception_code' => $exception->getCode()]);

        Log::error('Error processing FacebookPixel data');

        if ($exception->hasResponse()) {
            $errorResponse = $exception->getResponse();
            $contents = $errorResponse->getBody()->getContents() ?? null;

            Log::error('Error processing FacebookPixel data (with response)', [
                'contents' => $contents,
            ]);
        }
    }

    private function handleBadConfiguration(BadConfigurationException $exception)
    {
        Log::withContext(['success' => false]);
        Log::withContext(['exception_message' => $exception->getMessage()]);

        Log::debug('Bad Facebook Pixel configuration');
    }

}
