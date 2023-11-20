<?php

namespace App;

use App\File;
use App\Section;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    protected $fillable = ['name', 'folder', 'description', 'amount_of_fixed_content', 'thumb_id', 'platform', 'has_slide'];

    public function sections(){
        return $this->hasMany(Section::class);
    }

    public function thumb(){
        return $this->hasOne(File::class, 'id', 'thumb_id');
    }
}
