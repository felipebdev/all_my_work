<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Forum extends Model
{
    protected $fillable = ['id', 'image_id', 'thumb_id', 'active', 'theme', 'created_at', 'updated_at'];

    public function platform()
    {
        return $this->belongsTo(Platform::class);
    }

    public function file()
    {
        return $this->morphOne(File::class, 'filable');
    }

    // Icone
    public function thumb(){
        return $this->hasOne(File::class, 'id', 'thumb_id');
    }

    // Header do forum
    public function image()
    {
        return $this->hasOne(File::class, 'id', 'image_id');
    }
}
