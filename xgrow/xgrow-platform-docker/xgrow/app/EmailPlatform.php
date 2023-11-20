<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmailPlatform extends Model
{
    protected $fillable = ['email_id', 'platform_id', 'message', 'from', 'subject'];
}
