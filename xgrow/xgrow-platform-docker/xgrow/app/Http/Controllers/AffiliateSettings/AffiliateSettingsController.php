<?php

namespace App\Http\Controllers\AffiliateSettings;

use App\Http\Controllers\Controller;
use App\Http\Requests\AffiliateSettingsRequest;
use App\Http\Traits\CustomResponseTrait;
use App\Repositories\AffiliateSettings\AffiliateSettingsRepository;

class AffiliateSettingsController extends Controller
{
    use CustomResponseTrait;

    protected  $repository;

    public function __construct(AffiliateSettingsRepository $affiliateSettingsRepository)
    {
        $this->repository = $affiliateSettingsRepository;
    }

    public function createOrUpdate(AffiliateSettingsRequest $request)
    {
        try {
            $data = $this->repository->saveOrUpdate($request->all());

            return response()->json([
                'error' => false,
                'message' => $data['message'],
                'data' => $data
            ]);
        } catch (\Exception $e) {

            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }
}
