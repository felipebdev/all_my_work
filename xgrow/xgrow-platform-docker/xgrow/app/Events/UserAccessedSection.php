<?php

namespace App\Events;

use App\Section;
use App\Subscriber;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserAccessedSection
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $subscriber;
    public $section;

    public function __construct(Subscriber $subscriber, Section $section)
    {
        $this->subscriber = $subscriber;
        $this->section = $section;
    }
}
