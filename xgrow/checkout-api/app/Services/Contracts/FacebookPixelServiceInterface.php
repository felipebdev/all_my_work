<?php

namespace App\Services\Contracts;

use Psr\Http\Message\ResponseInterface;

interface FacebookPixelServiceInterface
{
    public function sendSingleRawData(array $data): ResponseInterface;
}
