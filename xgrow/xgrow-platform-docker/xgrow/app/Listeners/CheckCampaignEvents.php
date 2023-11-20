<?php

namespace App\Listeners;

use App\Events\UserAccessedContent;
use App\Events\UserAccessedCourse;
use App\Events\UserAccessedSection;
use App\Events\UserAccessedSite;
use App\Services\Campaign\CampaignService;
use Illuminate\Contracts\Queue\ShouldQueue;

class CheckCampaignEvents implements ShouldQueue
{

    public $connection = 'redis';
    public $queue = 'xgrow-jobs:campaign:automatic';

    private $campaignService;

    public function __construct(CampaignService $campaignService)
    {
        $this->campaignService = $campaignService;
    }

    public function subscribe($event)
    {
        $event->listen(UserAccessedSite::class, function (UserAccessedSite $event) {
            $this->campaignService->sendIfSiteAccessAffectsUser($event->subscriber);
        });

        $event->listen(UserAccessedContent::class, function (UserAccessedContent $event) {
            $this->campaignService->sendIfContentAccessAffectsUser($event->subscriber, $event->content);
        });

        $event->listen(UserAccessedCourse::class, function (UserAccessedCourse $event) {
            $this->campaignService->sendIfCourseAccessAffectsUser($event->subscriber, $event->course);
        });

        $event->listen(UserAccessedSection::class, function (UserAccessedSection $event) {
            $this->campaignService->sendIfSectionAccessAffectsUser($event->subscriber, $event->section);
        });
    }
}
