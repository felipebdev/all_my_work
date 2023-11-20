<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    protected $fillable = ['title', 'description', 'image_id', 'tags', 'forum_id',
        'active', 'moderation', 'created_at', 'updated_at'];

    public function forum()
    {
        return $this->hasOne(Forum::class, 'id', 'forum_id');
    }

    public function file()
    {
        return $this->morphOne(File::class, 'filable');
    }

    public function image()
    {
        return $this->hasOne(File::class, 'id', 'image_id');
    }

    public function posts()
    {
        return $this->hasMany(Post::class, 'topic_id', 'id');
    }

    public function posts_active()
    {
        return $this->hasMany(Post::class, 'topic_id', 'id')->where('approved', 1);
    }

    public function last_post()
    {
        return $this->hasMany(Post::class, 'topic_id', 'id')
            ->orderBy('updated_at', 'desc')
            ->with('subscribers:id,name,created_at,updated_at')
            ->with('platforms_users:id,name,created_at,updated_at');
    }
}
