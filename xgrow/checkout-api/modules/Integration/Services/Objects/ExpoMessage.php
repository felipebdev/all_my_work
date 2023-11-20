<?php

namespace Modules\Integration\Services\Objects;

class ExpoMessage
{
    public string $title;
    public string $body;

    public function __construct(string $title, string $body)
    {
        $this->title = $title;
        $this->body = $body;
    }

}
