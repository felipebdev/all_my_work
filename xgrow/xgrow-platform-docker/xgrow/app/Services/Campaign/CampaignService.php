<?php

namespace App\Services\Campaign;

use App\Campaign;
use App\Content;
use App\Course;
use App\Repositories\Campaign\AudienceConditionRepository;
use App\Section;
use App\Services\CampaignEmail\SendEmailService;
use App\Services\Contracts\SendSmsInterface;
use App\Services\Contracts\SendVoiceInterface;
use App\Services\Objects\PhoneResponse;
use App\Subscriber;

class CampaignService
{
    private $audienceConditionRepository;
    private $zenviaVoiceService;
    private $bulkgateSmsService;

    public function __construct(
        AudienceConditionRepository $audienceConditionRepository,
        SendVoiceInterface $sendVoice,
        SendSmsInterface $sendSms
    ) {
        $this->audienceConditionRepository = $audienceConditionRepository;
        $this->zenviaVoiceService = $sendVoice;
        $this->bulkgateSmsService = $sendSms;
    }

    public function sendIfSiteAccessAffectsUser(Subscriber $subscriber)
    {
        $campaigns = Campaign::where('type', Campaign::TYPE_AUTOMATIC)
            ->where('automatic_type', Campaign::FIRST_ACCESS_TO_THE_SITE)
            ->where('platform_id', $subscriber->platform_id)
            ->get();

        if ($campaigns) {
            $this->avaliateAutomaticCampaigns($campaigns, $subscriber);
        }
    }

    public function sendIfContentAccessAffectsUser(Subscriber $subscriber, Content $content)
    {
        $campaigns = Campaign::where('type', Campaign::TYPE_AUTOMATIC)
            ->where('automatic_type', Campaign::FIRST_ACCESS_TO_THE_CONTENT)
            ->where('automatic_id', $content->id)
            ->where('platform_id', $subscriber->platform_id)
            ->get();

        if ($campaigns) {
            $this->avaliateAutomaticCampaigns($campaigns, $subscriber);
        }
    }

    public function sendIfCourseAccessAffectsUser(Subscriber $subscriber, Course $course)
    {
        $campaigns = Campaign::where('type', Campaign::TYPE_AUTOMATIC)
            ->where('automatic_type', Campaign::FIRST_ACCESS_TO_THE_COURSE)
            ->where('automatic_id', $course->id)
            ->where('platform_id', $subscriber->platform_id)
            ->get();

        if ($campaigns) {
            $this->avaliateAutomaticCampaigns($campaigns, $subscriber);
        }
    }

    public function sendIfSectionAccessAffectsUser(Subscriber $subscriber, Section $section)
    {
        $campaigns = Campaign::where('type', Campaign::TYPE_AUTOMATIC)
            ->where('automatic_type', Campaign::FIRST_ACCESS_TO_THE_SECTION)
            ->where('automatic_id', $section->id)
            ->where('platform_id', $subscriber->platform_id)
            ->get();

        if ($campaigns) {
            $this->avaliateAutomaticCampaigns($campaigns, $subscriber);
        }
    }

    protected function avaliateAutomaticCampaigns(iterable $campaigns, Subscriber $subscriber)
    {
        foreach ($campaigns as $campaign) {
            foreach ($campaign->audiences as $audience) {
                $subscriberIsInAudience = $this->audienceConditionRepository->isSubscriberInAudience(
                    $subscriber->id,
                    $audience->id
                );

                if ($subscriberIsInAudience) {
                    $this->decideAndSendSingleCampaignMessage($campaign, $subscriber);
                }
            }
        }
    }

    public function decideAndSendCampaignMessages(Campaign $campaign)
    {
        switch($campaign->format){
            case Campaign::FORMAT_EMAIL:
                $this->dispatchEmailCampaign($campaign);
                break;
            case Campaign::FORMAT_AUDIO:
                $this->dispatchVoiceCampaign($campaign);
                break;
            case Campaign::FORMAT_SMS:
                $this->dispatchSmsCampaign($campaign);
                break;
        }
    }

    protected function decideAndSendSingleCampaignMessage(Campaign $campaign, Subscriber $subscriber)
    {
        switch($campaign->format){
            case Campaign::FORMAT_EMAIL:
                $this->dispatchSingleEmailCampaign($campaign, $subscriber);
                break;
            case Campaign::FORMAT_AUDIO:
                $this->dispatchSingleVoiceCampaign($campaign, $subscriber);
                break;
            case Campaign::FORMAT_SMS:
                $this->dispatchSingleSmsCampaign($campaign, $subscriber);
                break;
        }
    }

    protected function dispatchEmailCampaign(Campaign $campaign)
    {
        $emailRecipients = $this->getEmailRecipients($campaign);

        $sendEmailService = new SendEmailService($campaign->platform_id);
        $sendEmailService->sendEmailToRecipients(
            $campaign->subject,
            $campaign->text,
            $emailRecipients,
            $campaign->reply_to
        );

        $campaign->increment('sent', count($emailRecipients));
    }

    protected function dispatchSingleEmailCampaign(Campaign $campaign, Subscriber $subscriber)
    {
        $email = $subscriber->email;

        $sendEmailService = new SendEmailService($campaign->platform_id);
        $sendEmailService->sendEmailToRecipients($campaign->subject, $campaign->text, [$email], $campaign->reply_to);

        $campaign->increment('sent');
    }

    protected function dispatchVoiceCampaign(Campaign $campaign)
    {
        $phoneRecipients = $this->getPhoneRecipients($campaign);
        $result = $this->zenviaVoiceService->sendVoiceToNumbers($campaign->audio->filename, $phoneRecipients);
        $successful = array_filter($result, function (PhoneResponse $response) {
            return $response->isSuccessful();
        });
        $campaign->increment('sent', count($successful));
    }

    protected function dispatchSingleVoiceCampaign(Campaign $campaign, Subscriber $subscriber)
    {
        $phone = $subscriber->cel_phone;
        $this->zenviaVoiceService->sendVoiceToNumbers($campaign->audio->filename, [$phone]);
        $campaign->increment('sent');
    }

    protected function dispatchSmsCampaign(Campaign $campaign)
    {
        $phoneRecipients = $this->getPhoneRecipients($campaign);
        $result = $this->bulkgateSmsService->sendSmsToNumbers($campaign->text, $phoneRecipients);
        $successful = array_filter($result, function (PhoneResponse $response) {
            return $response->isSuccessful();
        });
        $campaign->increment('sent', count($successful));
    }

    protected function dispatchSingleSmsCampaign(Campaign $campaign, Subscriber $subscriber)
    {
        $phone = $subscriber->cel_phone;
        $this->bulkgateSmsService->sendSmsToNumbers($campaign->text, [$phone]);
        $campaign->increment('sent');
    }

    private function getEmailRecipients(Campaign $campaign)
    {
        return $this->getRecipients($campaign, 'email');
    }

    private function getPhoneRecipients(Campaign $campaign)
    {
        return $this->getRecipients($campaign, 'cel_phone', function ($number) {
            return preg_replace('/[^0-9]/', '', $number);
        });
    }

    private function getRecipients(Campaign $campaign, string $field, ?callable $map = null): array
    {
        $audiences = $campaign->audiences;
        $recipients = [];
        foreach ($audiences as $audience) {
            $query = $this->audienceConditionRepository->generateQueryByAudienceId($campaign->platform_id, $audience->id);
            $newRecipients = $query->get()->pluck($field)->map(function ($value) use ($map) {
                return is_callable($map) ? $map($value) : $value;
            })->toArray();
            $recipients = array_unique(array_merge($recipients, $newRecipients)); // merge without duplicated
        }

        return $recipients;
    }
}
