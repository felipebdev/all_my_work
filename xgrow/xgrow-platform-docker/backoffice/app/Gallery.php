<?php

namespace App;

use App\Image;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Gallery extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'description', 'created_by'];

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    public function images()
    {
        return $this->hasMany(Image::class);
    }

}
