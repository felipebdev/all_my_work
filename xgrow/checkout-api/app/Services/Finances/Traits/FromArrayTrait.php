<?php

namespace App\Services\Finances\Traits;

use App\Services\Finances\Contracts\SavesRawData;
use Illuminate\Support\Str;
use ReflectionClass;

/**
 * Simplify DTO's hydration using array data.
 *
 * This trait will try in a specific sequence, eg:
 * Using fromArray($data['subscriber_id'])
 *
 * withSubscriberId() method
 * subscriberId property
 * subscriber_id property
 *
 */
trait FromArrayTrait
{

    public static function fromArray(array $data): self
    {
        $selfClass = new ReflectionClass(static::class); // Resolve to the real class
        $self = new static();

        if ($selfClass->implementsInterface(SavesRawData::class)) {
            $self->setRawData($data);
        }

        foreach ($data as $property => $value) {
            $asCamel = Str::camel($property);
            $ucFirstCamel = ucfirst($asCamel);
            $withMethod = "with{$ucFirstCamel}";

            // by setter
            if ($selfClass->hasMethod($withMethod)) {
                $self->$withMethod($value);
                continue;
            }

            // exact property
            if ($selfClass->hasProperty($property)) {
                $self->{$property} = $value;
                continue;
            }

            // camel case
            if ($selfClass->hasProperty($asCamel)) {
                $self->{$asCamel} = $value;
                continue;
            }
        }

        return $self;
    }

}
