<?php

namespace App\Services\Integrations;

use Webpatser\Uuid\Uuid;
use Illuminate\Support\Facades\Redis;
use App\Services\Contracts\IntegrationServiceInterface;

class IntegrationService implements IntegrationServiceInterface {

    public function sendToBullMQ(string $queue, object $data) {
        $uuid = (string) Uuid::generate(4);
        Redis::command('hmset',["bull:{$queue}:$uuid", 'data' , json_encode($data)]); 
        Redis::command('zadd', ["bull:{$queue}:delayed", 1, $uuid]);
    }

}