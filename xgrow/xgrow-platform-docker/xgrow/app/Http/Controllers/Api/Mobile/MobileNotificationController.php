<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Http\Requests\MobileNotificationRequest;
use App\Repositories\MobileNotifications\MobileNotificationRepository;
use Illuminate\Http\JsonResponse;

/**
 *
 */
class MobileNotificationController extends Controller
{
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
        return response()->json([
            'number_unread_notifications' => $this->repository->getCountNotifications(),
            'notifications' => $this->repository->getNotificationsByUserPlatform(true)
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function show(): JsonResponse
    {
        return response()->json($this->repository->readMobileNotification(request()->route()->parameters()['notificationId']));
    }

    /**
     * @param MobileNotificationRequest $request
     * @return JsonResponse
     */
    public function update(MobileNotificationRequest $request): JsonResponse
    {
        return response()->json($this->repository->updateStatus(request()->route()->parameters()['notificationId'], $request->input('read')));
    }
}
