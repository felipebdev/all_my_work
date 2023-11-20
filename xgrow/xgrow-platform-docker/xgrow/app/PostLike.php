<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PostLike extends Model
{
    protected $table = 'post_like';

    protected $fillable = ['post_id', 'post_reply_id','subscribers_id', 'created_at', 'updated_at'];

    public function post()
    {
        return $this->hasOne(Post::class, 'id', 'post_id');
    }

    public function postReply()
    {
        return $this->hasOne(PostReply::class, 'id', 'post_reply_id');
    }

    public function subscribers()
    {
        return $this->hasOne(Subscriber::class, 'id', 'subscribers_id');
    }
}
