<?php

namespace Tests\Feature\Api\Email\Requests;

class EmailPutRequestTest extends EmailRequest
{
    protected string $method = 'putJson';
    protected string $endpoint = '/api/email/1';

    protected function getMethod(): string
    {
        return $this->method;
    }

    protected function getEndpoint(): string
    {
        return $this->endpoint;
    }
}
