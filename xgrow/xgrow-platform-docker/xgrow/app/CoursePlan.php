<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CoursePlan extends Model
{
    protected $fillable = ['course_id', 'plan_id'];
    protected $table = 'course_plan';

    public function course()
    {
        return $this->hasOne(Course::class, 'id', 'course_id');
    }

    public function plan()
    {
        return $this->hasOne(Plan::class,'id', 'plan_id');
    }
}
