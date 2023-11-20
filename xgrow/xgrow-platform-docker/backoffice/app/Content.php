<?php

namespace App;

use App\Author;
use App\Comment;
use App\Course;
use App\File;
use App\Module;
use App\Score;
use App\Section;
use App\Subscriber;
use App\Watched;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    use Sluggable, SluggableScopeHelpers, HasFactory;

    protected $fillable = ['title', 'subtitle', 'description', 'published', 'published_at', 'thumb_small_id', 'thumb_big_id', 'content_html', 'audio_link', 'video_link', 'external_link', 'has_audio_link', 'has_video_link', 'has_external_link', 'hashtags', 'available_after_content_id', 'expire_in', 'section_id', 'author_id', 'is_featured', 'featured_order', 'course_id', 'module_id', 'is_course'];

    public function sluggable()
    {
        return [
            'url' => [
                'source' => 'title'
            ]
        ];
    }

    public function section()
    {
    	return $this->belongsTo(Section::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    public function tracking()
    {
        return $this->hasMany(ContentTracking::class);
    }

    public function file()
    {
        return $this->morphOne(File::class, 'filable');
    }

    public function files()
    {
        return $this->morphMany(File::class, 'filable');
    }

    public function attachs()
    {
        return $this->morphMany(File::class, 'filable')->where('files.status', '=', '1');
    }

    public function thumb_small(){
        return $this->hasOne(File::class, 'id', 'thumb_small_id');
    }

    public function thumb_big(){
        return $this->hasOne(File::class, 'id', 'thumb_big_id');
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function scores()
    {
        return $this->morphMany(Score::class, 'scoreable');
    }

    public function views()
    {
        return $this->hasMany(View::class);
    }

    public function subscribers()
    {
        return $this->belongsToMany(Subscriber::class)->withTimestamps();
    }

    public function content_log(){
        return $this->hasMany(ContentLog::class, 'id', 'content_id');
    }

}
