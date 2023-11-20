<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = ['title', 'body', 'tags', 'approved',
        'topic_id', 'forum_id', 'platforms_users_id', 'views', 'subscribers_id',
        'created_at', 'updated_at'];

    public function forum()
    {
        return $this->hasOne(Forum::class, 'id', 'forum_id');
    }

    public function topic()
    {
        return $this->hasOne(Topic::class, 'id', 'topic_id');
    }

    public function platforms_users()
    {
        return $this->hasOne(PlatformUser::class, 'id', 'platforms_users_id')
            ->with('thumb:id,filename');
    }

    public function subscribers()
    {
        return $this->hasOne(Subscriber::class, 'id', 'subscribers_id')
            ->with('thumb:id,filename');
    }

    public function replies()
    {
        return $this->hasMany(PostReply::class, 'post_id', 'id');
    }

    public function last_replies()
    {
        return $this->hasMany(PostReply::class, 'post_id', 'id')
            ->orderBy('updated_at', 'desc')
            ->with('subscribers:id,name,created_at,updated_at')
            ->with('platforms_users:id,name,created_at,updated_at');
    }

    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = htmlentities($value);
    }

    public function setBodyAttribute($value)
    {
        $this->attributes['body'] = htmlentities($value);
    }

    public function setTagsAttribute($value)
    {
        $this->attributes['tags'] = htmlentities($value);
    }
}
