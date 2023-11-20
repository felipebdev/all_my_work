<?php

namespace App;

use App\Question;
use Illuminate\Database\Eloquent\Model;

class QuestionOption extends Model
{
    protected $fillable = ["description", "correct", "question_id"];

    public function question(){
    	return $this->belongsTo(Question::class);
    }
}
