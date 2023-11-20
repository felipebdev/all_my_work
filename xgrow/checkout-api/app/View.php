<?php

namespace App;

use App\Subscriber;
use App\Content;
use Illuminate\Database\Eloquent\Model;

class View extends Model
{
    protected $fillable = ['subscriber_id', 'content_id'];

    public function subscriber()
    {
        return $this->belongsTo(Subscriber::class);
    }

    public function content()
    {
        return $this->belongsTo(Content::class);
    }
}
