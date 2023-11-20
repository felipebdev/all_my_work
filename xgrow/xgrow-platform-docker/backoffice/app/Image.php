<?php

namespace App;

use App\Gallery;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $fillable = ['filename', 'size', 'type', 'original_name', 'gallery_id'];

    protected $dates = ['created_at', 'updated_at'];

    public function gallery()
    {
        return $this->belongsTo(Gallery::class);
    }
}
