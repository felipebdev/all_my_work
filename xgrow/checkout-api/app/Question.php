<?php

namespace App;

use App\File;
use App\QuestionOption;
use App\Quiz;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{

	protected $fillable = ["description", "type", "order", "quiz_id", "thumb_id"];

    const TYPE_UNIQUE = 1;
    const TYPE_MULTIPLE = 2;
    const TYPE_TEXT = 3;

    public static function listTypes() {
        return array(
            self::TYPE_UNIQUE => 'Resposta única', 
            self::TYPE_MULTIPLE => 'Resposta múltipla', 
            self::TYPE_TEXT => 'Resposta em texto'
        );
    }

    public function options(){
    	return $this->hasMany(QuestionOption::class);
    }

    public function thumb(){
        return $this->hasOne(File::class, 'id', 'thumb_id');
    }

    public function quiz(){
    	return $this->belongsTo(Quiz::class);
    }
}
