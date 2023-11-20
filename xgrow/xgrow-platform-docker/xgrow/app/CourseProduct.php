<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CourseProduct extends Model
{
    public $timestamps = false;

    protected $table = 'course_product';

    protected $fillable = ['course_id', 'product_id', 'content_course_id'];
}
