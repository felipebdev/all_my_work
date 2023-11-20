<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NotesSubscribers extends Model
{
    protected $fillable = [
        'id','note','subscriber_id', 'content_id'
    ];

    protected $table = 'notes_subscribers';
}
