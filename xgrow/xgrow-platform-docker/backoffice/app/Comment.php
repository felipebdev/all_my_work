<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = ['platform_id', 'subscriber_id', 'contents_id', 'text', 'id_comment_sub', 'like', 'views'];

    public function commentable()
    {
        return $this->morphTo();
    }
}
