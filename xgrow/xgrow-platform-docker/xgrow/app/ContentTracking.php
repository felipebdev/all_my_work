<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContentTracking extends Model
{
	protected $table = "contents_trackings";
    protected $fillable = ['likes', 'unlikes', 'views', 'content_id'];

    public function content()
    {
    	return $this->belongsTo(Content::class);
    }

}
