<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PostReply extends Model
{
    protected $table = 'post_reply';

    protected $fillable = ['title', 'body', 'tags', 'approved',
        'post_id', 'platforms_users_id', 'subscribers_id',
        'created_at', 'updated_at'];

    public function post()
    {
        return $this->hasOne(Post::class, 'id', 'post_id');
    }

    public function platforms_users()
    {
        return $this->hasOne(PlatformUser::class, 'id', 'platforms_users_id')
            ->with('thumb:id,filename');
    }

    public function subscribers()
    {
        return $this->hasOne(Subscriber::class, 'id', 'subscribers_id')
            ->select(['id', 'name', 'email', 'thumb_id', 'created_at', 'updated_at'])
            ->with('thumb:id,filename');
    }
}
