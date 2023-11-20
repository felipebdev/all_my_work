<?php

namespace App\Services\LA;

use Exception;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FirstStepService
{
    private LaProducerBaseService $laProducerBaseService;

    /**
     * @param LaProducerBaseService $laProducerBaseService
     * @return void
     */
    public function __construct(LaProducerBaseService $laProducerBaseService)
    {
        $this->laProducerBaseService = $laProducerBaseService;
    }

    /**
     * Get All FirstStep by PlataformId
     *
     * Tip :: If necessary in the future, add platformId for get more than one.
     */
    public function getFirstStepByPlatformId()
    {
        try {
            $req = $this->laProducerBaseService->connectionConfig(Auth::user()->platform_id, Auth::user()->id);
            $res = $req->get('platform-config/first-step', ['query' => ['platformId' => Auth::user()->platform_id]]);
            $content = $res->getBody()->getContents();

            Log::info('Retrieve First Step', [
                'message' => $content,
                'method' => 'GET',
                'uri' => 'platform-config/first-step?platformId=' . Auth::user()->platform_id,
                'platformId' => Auth::user()->platform_id,
                'code' => $res->getStatusCode()
            ]);

            return json_decode($content)->data[0] ?? null;
        } catch (Exception $e) {
            Log::error('Retrieve First Step (Onboard)', ['msg' => $e->getMessage(), 'code' => $e->getCode()]);
        } catch (GuzzleException $e) {
            Log::error('Retrieve First Step (Onboard)', ['msg' => $e->getMessage(), 'code' => $e->getCode()]);
        }
    }

    /**
     * Create first step on LA
     *
     * @param $data
     * @return string|void|null
     */
    public function saveFirstStep($data)
    {
        try {
            $req = $this->laProducerBaseService->connectionConfig(Auth::user()->platform_id, Auth::user()->id);
            $data['platformId'] = Auth::user()->platform_id;
            $res = $req->put('platform-config/first-step', ['json' => $data, 'query' => ['platformId' => Auth::user()->platform_id]]);
            $content = $res->getBody()->getContents();

            Log::info('Save First Step', [
                'message' => $content,
                'method' => 'PUT',
                'uri' => 'platform-config/first-step?platformId=' . Auth::user()->platform_id,
                'platformId' => Auth::user()->platform_id,
                'code' => $res->getStatusCode()
            ]);

            return json_decode($content) ?? null;
        } catch (ClientException $e) {
            Log::error('Save First Step (Onboard)', ['msg' => $e->getMessage(), 'code' => $e->getCode()]);
        } catch (GuzzleException $e) {
            Log::error('Save First Step (Onboard)', ['msg' => $e->getMessage(), 'code' => $e->getCode()]);
        }
    }
}
