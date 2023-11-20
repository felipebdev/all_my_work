<?php

namespace App\Rules\Objects;

use App\Rules\Contracts\MessageStrategyInterface;

class SubscriptionActiveStrategy implements MessageStrategyInterface {

    private $strategy;

    public function __construct(MessageStrategyInterface $strategy)
    {
        $this->strategy = $strategy;
        return $this;
    }

    public function getMessage() : string 
    {
        return $this->strategy->getMessage();
    }
}