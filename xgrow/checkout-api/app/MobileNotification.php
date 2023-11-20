<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class MobileNotification extends Model
{
    use HasFactory;

    protected static function booted()
    {
        // auto generate UUID for model
        static::creating(fn(self $model) => $model->id = (string) Uuid::uuid4());
    }

    public $keyType = 'string';

    public $incrementing = false;

    public $fillable = [
        'created_at',
        'updated_at',
        'platform_id',
        'platforms_users_id',
        'title',
        'body',
        'read',
    ];

}
