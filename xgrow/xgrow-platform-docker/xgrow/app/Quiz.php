<?php

namespace App;

use App\Platform;
use App\Question;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    protected $fillable = [
        'name', 'description', 'platform_id', 'thumb_id'
    ];

    const QUESTION_TOTAL_DEFAULT = '4'; 

    public function platform(){
    	return $this->belongsTo(Platform::class);
    }

    public function thumb(){
        return $this->hasOne(File::class, 'id', 'thumb_id');
    }

    public function questions(){
    	return $this->hasMany(Question::class);
    }
}
