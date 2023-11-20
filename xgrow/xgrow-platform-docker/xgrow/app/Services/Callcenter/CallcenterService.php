<?php

namespace App\Services\Callcenter;

use App\Attendance;
use App\Attendant;
use App\Audience;
use App\CallcenterConfig;
use App\Repositories\Campaign\AudienceConditionRepository;

class CallcenterService
{
    private $audienceConditionRepository;

    public function __construct(AudienceConditionRepository $audienceConditionRepository)
    {
        $this->audienceConditionRepository = $audienceConditionRepository;
    }

    /**
     * Deliver Leads to Attendant
     *
     * @param  int  $attendantId Attendant ID
     * @return int Number of leads delivered
     */
    public function deliverLeadsByAttendant(int $attendantId): int
    {
        $attendant = Attendant::findOrFail($attendantId);
        $platformId = $attendant->platform_id;

        $callcenterConfig = CallcenterConfig::where('platform_id', $platformId)->first();
        $numberLeads = $callcenterConfig->number_leads ?? 5;

        //$this->resetAttendances($attendant);

        $attendancesOnQueue = $attendant->attendancesWithoutGlobalScopes()
                                        ->where('queue', true)
                                        ->where('audience_id', $attendant->audience_id)
                                        ->get();

        $addLeads = $numberLeads - $attendancesOnQueue->count();

        if ($addLeads <= 0) {
            return 0;
        }

        $this->removeDoneAudiences($attendant);

        $audiencesSubscribers = $this->audienceConditionRepository->subscribersFromAudienceIds($platformId, [
            $attendant->audience_id
        ]);

        $attendances = $attendant->attendances()
                                 ->where('status', '<>', Attendance::STATUS_EXPIRED)
                                 ->where('audience_id', $attendant->audience_id)
                                 ->get();

        $subscribersWithAttendant = $attendances->pluck('subscriber_id');

        $subscribersWithoutAttendant = $audiencesSubscribers->whereNotIn('id', $subscribersWithAttendant);

        $finalNumber = min($subscribersWithoutAttendant->count(), $addLeads);

        $subscribers = $subscribersWithoutAttendant->random($finalNumber);

        $attendant->attendances()->createMany($subscribers->map(function ($subscriber) use ($attendant) {
            return [
                'subscriber_id' => $subscriber['id'],
                'audience_id' => $subscriber['audience_id'],
                'payment_id' => $subscriber['payment_id'] ?? null,
            ];
        }));

        return $finalNumber;
    }

    /**
     * Excludes attendances that are not on the audience list
     *
     * @param $attendant
     */
    public function resetAttendances($attendant)
    {
        $audienceId =  $this->getAudienceId($attendant->id);

        $attendances = $attendant->attendances()
                                 ->where('status', Attendance::STATUS_PENDING)
                                 ->where('audience_id', '<>', $audienceId)
                                 ->get();

        foreach($attendances as $attendance){
            $attendance->contacts()->delete();
            $attendance->delete();
        }
    }

    /**
     * Get Audiences by attendant
     * @param $attendant
     * @return mixed
     */
    private function getAudiences($attendant)
    {
        $audiences = $attendant->allaudience
            ? Audience::where('platform_id', $attendant->platform_id)
            : $attendant->audiences();

        return $audiences->where(function ($query) {
            $query->where('callcenter_active', true)
                ->orWhereNull('callcenter_active');
        })->pluck('id');
    }

    /**
     * Get audience_id from attendant
     * @param int $attendantId
     * @return null
     */
    private function getAudienceId(int $attendantId)
    {
        $attendant = Attendant::findOrFail($attendantId);

        if($attendant->audience)
            return $attendant->audience->id;

        $audiences = $attendant->allaudience
            ? Audience::where('platform_id', $attendant->platform_id)
            : $attendant->audiences();

        $audience = $audiences->where(function ($query) {
            $query->where('callcenter_active', true)
                ->orWhereNull('callcenter_active');
        });

        return $audience->count() > 0 ? $audience->first()->id : null;
    }

    private function removeDoneAudiences($attendant)
    {
        $platformId = $attendant->platform_id;

        $audiences = $this->getAudiences($attendant);

        $audiencesToEnd = $audiences;
        $audiences = $audiences->reject(function ($audience) use ($platformId) {
            $audienceWithConditions = Audience::select()
                ->with('audienceConditions')
                ->where('id', '=', $audience)
                ->get()
                ->first();

            $leads = $this->audienceConditionRepository->generateQueryFromArray(
                $platformId,
                $audienceWithConditions->audienceConditions->toArray()
            )->get()->count();
            $totalContacted = Attendance::where('audience_id', '=', $audience)->where('status', '<>', Attendance::STATUS_PENDING)->count();

            return $leads == $totalContacted;
        });

        $this->endAudience(array_diff($audiencesToEnd->toArray(), $audiences->toArray()));
        return $audiences;
    }

    private function endAudience($audiences): void
    {
        $audiences = Audience::select()->whereIn('id', $audiences)->get();
        $date = date('Y-m-d H:i:s');
        foreach ($audiences as $audience) {
            $audience->callcenter_active = false;
            $audience->callcenter_end_date = $date;
            $audience->save();
        }
    }
}
