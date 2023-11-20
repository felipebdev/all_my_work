<?php

namespace App\Repositories\AffiliateSettings;

use App\AffiliationSettings;
use Illuminate\Support\Str;

/**
 *
 */
class AffiliateSettingsRepository
{
    /**
     * @var AffiliationSettings
     */
    protected $model;

    /**
     * @param AffiliationSettings $affiliateSettings
     */
    public function __construct(AffiliationSettings $affiliateSettings)
    {
        $this->model = $affiliateSettings;
    }


    /**
     * @param array $request
     * @return array
     */
    public function saveOrUpdate(array $request): array
    {
        $invite = Str::uuid();

        $getAffiliateSettings = $this->model->getAffiliateSettings($request['product_id']);

        if (!$getAffiliateSettings) {

            $request['invite_link'] = $request['invite_link'] ?? $invite;

            return $this->createAffiliate($request);
        }

        $request['invite_link'] = $getAffiliateSettings->invite_link ?? $invite;

        return $this->updateAffiliate($getAffiliateSettings->id, $request);
    }

    /**
     * @param array $request
     * @return array
     */
    public function createAffiliate(array $request): array
    {
        $data = $this->model->create($request);

        return ['message' => 'Informações de afiliados criada com sucesso', 'data' => $data];
    }

    /**
     * @param int $id
     * @param array $request
     * @return array
     */
    public function updateAffiliate(int $id, array $request): array
    {
        $data = $this->model->find($id)->update($request);

        return ['message' => 'Informações de afiliados atualizada com sucesso', 'data' => $this->model->find($id)];
    }
}
