<?php

namespace App;

use App\Course;
use App\Subscriber;
use Illuminate\Database\Eloquent\Model;

class CourseSubscriber extends Model
{
   protected $fillable = [
        'total_classes_attended', 'certificate', 'course_id', 'subscriber_id', 'note', 'token', 'certificated_at'
    ];

    public function course()
    {
    	return $this->belongsTo(Course::class);
    }

    public function subscriber()
    {
    	return $this->belongsTo(Subscriber::class);
    }


}
