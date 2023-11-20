<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmailPlatform extends Model
{
    protected $fillable = ['message', 'from', 'email_id', 'platform_id'];
}
