<?php

namespace App;

use App\Content;
use App\File;
use App\Platform;
use App\Template;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;

class Section extends Model
{
    use Sluggable;
    use SluggableScopeHelpers;

    public function sluggable()
    {
        return [
            'name_slug' => [
                'source' => 'name'
            ]
        ];
    }

    protected $casts = [
        'section_key' => 'string'
    ];

    protected $fillable = [
        'name', 'description', 'url', 'thumb_id', 'active', 'allow_comments', 'allow_likes', 'allow_trackings',
        'section_key', 'name_slug', 'orderby_id', 'platform_id', 'template_id','featured_order',
        'content_title',"content_subtitle","content_author",'content_description','qtd_per_page','content_template_id',
        'section_title',"section_subtitle","section_author",'section_description','section_qtd_per_page'
    ];

    public function file()
    {
        return $this->morphOne(File::class, 'filable');
    }

    public function thumb(){
        return $this->hasOne(File::class, 'id', 'thumb_id');
    }

    public function platform(){
        return $this->belongsTo(Platform::class);
    }

    public function template(){
        return $this->belongsTo(Template::class);
    }

    public function contents(){
        return $this->hasMany(Content::class);
    }

}
