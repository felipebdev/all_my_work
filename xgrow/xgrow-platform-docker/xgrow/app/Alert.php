<?php

namespace App;

use App\Course;
use App\Subscriber;
use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    protected $fillable = [
        'name', 'description', 'expires_in', 'course_id'
    ];

    public function course(){
    	return $this->belongsTo(Course::class);
    }

    public function subscribers()
    {
        return $this->belongsToMany(Subscriber::class)->withTimestamps();
    }
}
