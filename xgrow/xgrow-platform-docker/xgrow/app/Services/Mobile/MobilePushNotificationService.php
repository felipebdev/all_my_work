<?php

namespace App\Services\Mobile;

use App\Exceptions\NotificationInvalidException;
use App\PushNotification;
use App\Repositories\Mobile\Filter\PushNotificationFilter;
use App\Repositories\Mobile\PushNotificationRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class MobilePushNotificationService
{
    private PushNotificationRepository $pushNotificationRepository;

    public function __construct(PushNotificationRepository $pushNotificationRepository)
    {
        $this->pushNotificationRepository = $pushNotificationRepository;
    }

    /**
     * List mobile push notifications for an specific platform
     *
     * @param  string  $platformId
     * @param  array|null  $filter
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function listPlatformMobileNotifications(string $platformId, ?array $filter = []): Collection
    {
        $pushNotificationFilter = PushNotificationFilter::fromArray($filter);
        $pushNotificationFilter->platform_id = $platformId; // enforce condition

        return $this->pushNotificationRepository->listAllMobileNotifications($pushNotificationFilter);
    }

    /**
     * @param  string  $platformId
     * @param  string  $userId
     * @param  string  $title
     * @param  string  $text
     * @param  \DateTimeInterface  $runAt
     * @return \App\PushNotification
     * @throws \App\Exceptions\NotificationInvalidException
     */
    public function addNewMobileNotificationOnPlatform(
        string $platformId,
        string $userId,
        string $title,
        string $text,
        \DateTimeInterface $runAt
    ): PushNotification {
        if (Carbon::now()->isAfter($runAt)) {
            throw new NotificationInvalidException('Data de agendamento deve ser futura', 400);
        }

        return $this->pushNotificationRepository->createMobileNotification($platformId, $userId, $title, $text, $runAt);
    }


    /**
     * @param  string  $notificationId
     * @param  string|null  $title
     * @param  string|null  $text
     * @param  \DateTimeInterface|null  $runAt
     * @return \App\PushNotification
     * @throws \App\Exceptions\NotificationInvalidException
     */
    public function updatePartially(
        string $notificationId,
        ?string $title,
        ?string $text,
        ?\DateTimeInterface $runAt
    ): PushNotification {
        if ($runAt && Carbon::now()->isAfter($runAt)) {
            throw new NotificationInvalidException('Data de agendamento deve ser futura', 400);
        }

        $isSent = $this->pushNotificationRepository->isNotificationSent($notificationId);

        if (is_null($isSent)) {
            throw new NotificationInvalidException('Notificação não encontrada', 404);
        }

        if ($isSent) {
            throw new NotificationInvalidException('Notificação já enviada não pode ser alterada', 409);
        }

        $options = array_filter([
            'title' => $title,
            'text' => $text,
            'run_at' => $runAt,
        ], fn($value) => !is_null($value));

        return $this->pushNotificationRepository->updateMobileNotificationById($notificationId, $options);
    }

    /**
     * @param  string  $notificationId
     * @return bool
     * @throws \App\Exceptions\NotificationInvalidException
     */
    public function destroyNotification(string $notificationId): bool
    {
        $isSent = $this->pushNotificationRepository->isNotificationSent($notificationId);

        if (is_null($isSent)) {
            throw new NotificationInvalidException('Notificação não encontrada', 404);
        }

        if ($isSent) {
            throw new NotificationInvalidException('Notificação já enviada não pode ser alterada', 409);
        }

        $deleted = $this->pushNotificationRepository->softDeleteById($notificationId);

        if (!$deleted) {
            throw new NotificationInvalidException('Falha desconhecida ao remover', 500);
        }

        return true;
    }

}
