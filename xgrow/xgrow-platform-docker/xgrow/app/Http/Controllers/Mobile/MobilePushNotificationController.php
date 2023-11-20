<?php

namespace App\Http\Controllers\Mobile;

use App\Exceptions\NotificationInvalidException;
use App\Helpers\CollectionHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Mobile\MobilePushNotificationStoreRequest;
use App\Http\Requests\Mobile\MobilePushNotificationUpdateRequest;
use App\Http\Traits\CustomResponseTrait;
use App\Services\Mobile\MobilePushNotificationService;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class MobilePushNotificationController extends Controller
{
    use CustomResponseTrait;

    private MobilePushNotificationService $mobilePushNotificationService;

    public function __construct(MobilePushNotificationService $mobilePushNotificationService)
    {
        $this->mobilePushNotificationService = $mobilePushNotificationService;
    }

    public function index(Request $request)
    {
        $platformId = Auth::user()->platform_id;
        $offset = $request->input('offset') ?? 50;

        $pushNotifications = $this->mobilePushNotificationService->listPlatformMobileNotifications(
            $platformId,
            $request->toArray()
        );

        return $this->customJsonResponse('Dados carregados com sucesso.', Response::HTTP_OK, [
            'push_notifications' => CollectionHelper::paginate($pushNotifications, $offset)
        ]);
    }

    public function store(MobilePushNotificationStoreRequest $request)
    {
        try {
            $notification = $this->mobilePushNotificationService->addNewMobileNotificationOnPlatform(
                Auth::user()->platform_id,
                Auth::user()->id,
                $request->title,
                $request->text,
                CarbonImmutable::parse($request->run_at)
            );

            return $this->customJsonResponse(
                'Notificação criada com sucesso',
                Response::HTTP_OK,
                $notification->toArray()
            );
        } catch (NotificationInvalidException $e) {
            return $this->customJsonResponse($e->getMessage(), $e->getCode());
        }
    }

    public function update($notification_id, MobilePushNotificationUpdateRequest $request)
    {
        try {
            $runAt = is_null($request->run_at) ? null : CarbonImmutable::parse($request->run_at);

            $notification = $this->mobilePushNotificationService->updatePartially(
                $notification_id,
                $request->title,
                $request->text,
                $runAt,
            );

            return $this->customJsonResponse(
                'Notificação atualizada com sucesso',
                Response::HTTP_OK,
                $notification->toArray()
            );
        } catch (NotificationInvalidException $e) {
            return $this->customJsonResponse($e->getMessage(), $e->getCode());
        }
    }

    public function destroy($notification_id)
    {
        try {
            $this->mobilePushNotificationService->destroyNotification($notification_id);

            return $this->customJsonResponse('Notificação removida com sucesso');
        } catch (NotificationInvalidException $e) {
            return $this->customJsonResponse($e->getMessage(), $e->getCode());
        }
    }

}
