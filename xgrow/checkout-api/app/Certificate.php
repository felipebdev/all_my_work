<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    protected $fillable = [
        'course_id', 'active', 'condition_watch', 'condition_answer_questions', 'template_id', 'has_image', 'image_id'
    ];

    public function course(){
    	return $this->belongsTo(Course::class);
    }

    public function image(){
    	return $this->hasOne(File::class, 'id', 'image_id');
    }
}
