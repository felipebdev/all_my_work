<?php

namespace App\Http\Controllers\MobileNotification;

use App\Http\Requests\MobileNotificationRequest;
use App\Http\Traits\CustomResponseTrait;
use App\Repositories\MobileNotifications\MobileNotificationRepository;
use Illuminate\Http\JsonResponse;

/**
 *
 */
class MobileNotificationController
{
    use CustomResponseTrait;
    /**
     * @var MobileNotificationRepository
     */
    protected MobileNotificationRepository $repository;

    /**
     * @param MobileNotificationRepository $mobileNotificationRepository
     */
    public function __construct(MobileNotificationRepository $mobileNotificationRepository)
    {
        $this->repository = $mobileNotificationRepository;
    }

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $return = $this->customJsonResponse(
            'Sem notificações recentes',
            404,
        );

        if (sizeof($this->repository->getNotificationsByUserPlatform()) > 0) {

            $return = $this->customJsonResponse(
                'Dados carregados com sucesso',
                200,
                ['data' => $this->repository->getNotificationsByUserPlatform()]
            );
        }

        return $return;
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        try {

            return $this->customJsonResponse(
                'Dados carregados com sucesso',
                200,
                ['data' => $this->repository->readMobileNotification($id)]
            );
        } catch (\Exception $ex) {

            return $this->customJsonResponse(
                'Notificação não encontrada',
                404,
            );
        }
    }

    /**
     * @param MobileNotificationRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(MobileNotificationRequest $request, $id)
    {
        try {

            return $this->customJsonResponse(
                'Dados carregados com sucesso',
                200,
                ['data' => $this->repository->updateStatus($id, $request->input('read'))]
            );
        } catch (\Exception $ex) {

            return $this->customJsonResponse(
                'Notificação não encontrada',
                404,
            );
        }
    }
}
