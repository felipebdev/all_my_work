<?php


namespace App\Repositories\LA;

use App\Course;
use App\Repositories\BaseRepository;
use App\Subscriber;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class LaConsumerRepository extends BaseRepository
{
    public function model()
    {
        return Subscriber::class;
    }

    /**
     * Get subscriber list for LA Consumer
     * @param $platformId
     * @param array $subscriberIds
     * @return Builder
     */
    public function getSubscriberList($platformId, $subscriberIds): Builder
    {
        $query = Subscriber::select(['id', 'name', 'email'])
            ->where('platform_id', $platformId)
            ->where('status', 'active');

        if ($subscriberIds) {
            $query->whereIn('id', $subscriberIds);
        }

        return $query;
    }

    /**
     * Get course list for LA Consumer
     * @param $platformId
     * @param array $courseIds
     * @return mixed
     */
    public function getCourseList($platformId, $courseIds): Builder
    {
        $query = Course::select(['id', 'name AS courseName'])
            ->where('platform_id', $platformId)
            ->with('customModules:id,name AS moduleName,course_id');

        if ($courseIds) {
            $query->whereIn('id', $courseIds);
        }

        return $query;
    }

    /**
     * Get subscriber list for LA Consumer
     * @param array $subscriberIds
     */
    public function updateSubscriberLastAccessList(array $subscriberIds)
    {
        foreach ($subscriberIds as $subscriber) {
            $subscriberFound = Subscriber::select(['id', 'login', 'last_acess'])
//                ->where('platform_id', $platformId)
                ->where('status', 'active')
                ->where('id', $subscriber['id'])->first();
            $subscriberFound->login = Carbon::parse($subscriber['lastAccess'])->toDateTimeString();
            $subscriberFound->last_acess = Carbon::parse($subscriber['lastAccess'])->toDateTimeString();
            $subscriberFound->save();
        }
    }

    /**
     * Update Subscriber Expo LA Token
     * @param int $subscriber_id
     * @param string $platform_id
     * @param string $expo_la_token
     * @return void
     */
    public function updateSubscriberExpoLAToken(int $subscriberId, string $expoLAToken)
    {
        $subscriber = Subscriber::findOrFail($subscriberId);
        $subscriber->expo_la_token = $expoLAToken;
        $subscriber->save();
    }
}
