<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContentSubscriber extends Model
{
    protected $table="content_subscriber";
    protected $fillable = ['update_at', 'subscriber_id', 'content_id', 'concluded'];
}
