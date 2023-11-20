<?php

namespace App\Events;

use App\Course;
use App\Subscriber;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserAccessedCourse
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $subscriber;
    public $course;

    public function __construct(Subscriber $subscriber, Course $course)
    {
        $this->subscriber = $subscriber;
        $this->course = $course;
    }
}
