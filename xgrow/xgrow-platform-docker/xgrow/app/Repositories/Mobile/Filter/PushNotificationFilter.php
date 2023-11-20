<?php

namespace App\Repositories\Mobile\Filter;

use App\Services\Objects\FillableObject;

class PushNotificationFilter extends FillableObject
{
    public ?string $platform_id = null;

    public ?string $search = null; // search using "like" operator in many columns (slow)
    public ?string $title = null; // identical match
    public ?string $text = null; // identical match
    public ?string $run_after = null;
    public ?string $run_before = null;
    public ?bool $is_sent = null;
    public array $user_id = [];
    public array $type = [];
    public ?string $created_after = null;
    public ?string $created_before = null;
    public ?string $updated_after = null;
    public ?string $updated_before = null;

    public static function empty()
    {
        return new self();
    }

    public static function fromArray(array $request)
    {
        $self = new self();
        $self->fill($request);
        return $self;
    }

}