<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Author extends Model
{

    protected $fillable = [
        'name_author',
        'author_photo',
        'author_desc',
        'author_email',
        'author_insta',
        'author_linkedin',
        'author_youtube',
        'status',
        'platform_id'
    ];

    public function platform()
    {
        return $this->belongsTo(Platform::class);
    }
}
