<?php

namespace App\Services\LearningAreaAPI;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LearningAreaService
{
    /**
     * Function responsable for generate producer token
     * @param string $platformId
     * @param string $producerId
     * @return mixed
     */
    private function generateProducerToken(string $platformId, string $producerId)
    {
        try {
            $payload = ["platformId" => $platformId, "producerId" => $producerId];
            $response = Http::learningArea()->post('auth/producer', $payload);

            Log::info('Get Token', [
                'message' => json_encode($response->json()),
                'method' => 'POST',
                'uri' => 'auth/producer',
                'platformId' => $platformId,
                'producerId' => $producerId,
                'code' => $response->clientError()
            ]);

            return $response->json('token');
        } catch (Exception $e) {
            Log::error('Get Token', ['msg' => json_encode($e), 'code' => $e->getCode()]);
            throw new Exception("Get Token:" . $e->getMessage(), 400);
        }
    }

    /**
     * Create template on Learning Area
     * @param string $platformId
     * @param string $producerId
     * @return mixed
     */
    public function setLearningAreaTheme(string $platformId, string $producerId, array $defaultTheme)
    {
        try {
            $token = $this->generateProducerToken($platformId, $producerId);
            $response = Http::learningArea()->withToken($token)->post('platform-config/theme', $defaultTheme);

            Log::info('Set Learning Area Theme', [
                'message' => json_encode($response->json()),
                'method' => 'POST',
                'uri' => 'auth/producer',
                'platformId' => $platformId,
                'producerId' => $producerId,
                'code' => $response->clientError()
            ]);

            return $response->json();
        } catch (Exception $e) {
            Log::error('Set Learning Area Theme', ['msg' => json_encode($e), 'code' => $e->getCode()]);
            throw new Exception("Get Token:" . $e->getMessage(), 400);
        }
    }
}
