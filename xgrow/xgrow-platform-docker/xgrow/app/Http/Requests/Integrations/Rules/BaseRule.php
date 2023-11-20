<?php

namespace App\Http\Requests\Integrations\Rules;

abstract class BaseRule {
    private $rules = [];
    private $attributes = [];

    public abstract function getRules();
    public abstract function getAttributes();
}