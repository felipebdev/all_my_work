<?php

namespace App\Services\Contracts;

interface IntegrationServiceInterface {
    public function sendToBullMQ(string $queue, object $data);
}