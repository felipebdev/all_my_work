<?php

namespace App;

use App\File;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    protected $fillable = ['name', 'folder', 'description', 'amount_of_fixed_content', 'thumb_id','platform','content','content_model','has_slide', 'course_model','course','tamanho_imagem'];

    public function file()
    {
        return $this->morphOne(File::class, 'filable');
    }

    public function thumb(){
        return $this->hasOne(File::class, 'id', 'thumb_id');
    }
}
