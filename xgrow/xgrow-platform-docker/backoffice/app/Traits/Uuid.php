<?php

namespace App\Traits;

use Ramsey\Uuid\Uuid as UuidGenerator;

trait Uuid
{
    public static function bootUuid()
    {   
        // Generate uuid primary key 
        static::creating(function ($model) {
            if (!$model->getKey()) {
                $model->{$model->getKeyName()} = (string) UuidGenerator::uuid4();
            }
        });
    }
}