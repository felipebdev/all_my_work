<?php

namespace App\Events;

use App\Content;
use App\Subscriber;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserAccessedContent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $subscriber;
    public $content;

    public function __construct(Subscriber $subscriber, Content $content)
    {
        $this->subscriber = $subscriber;
        $this->content = $content;
    }
}
