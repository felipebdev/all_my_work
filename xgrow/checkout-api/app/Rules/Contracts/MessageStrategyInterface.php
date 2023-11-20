<?php

namespace App\Rules\Contracts;

interface MessageStrategyInterface {
    public function getMessage() : string;
}