<?php

namespace App;

use App\Watched;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Content extends Model
{
    use Sluggable;
    use SluggableScopeHelpers;
    use SoftDeletes;

    //    protected static function boot()
    //    {
    //        parent::boot();
    ////        static::deleted(function ($content) {
    ////
    ////            $item_type = ($content->is_course == 1) ? 2 : 3;
    ////            $platform_id = ($content->is_course == 1) ? $content->course->platform_id : $content->section->platform_id;
    ////
    ////            $items['item_type'] = $item_type;
    ////            $items['platform_id'] = $platform_id;
    ////            $items['item_id'] = $content->id;
    ////            Menu::deleteItem($items);
    ////
    ////            $items['model_type'] = $item_type;
    ////            $items['model_id'] = $content->id;
    ////            Widget::deleteItem($items);
    ////
    ////        });
    ////
    ////        static::deleting(function ($content) {
    ////            Comment::where('contents_id', $content->id)->delete();
    ////        });
    //    }

    protected $fillable = ['title', 'subtitle', 'description', 'published', 'published_at', 'thumb_small_id', 'thumb_big_id', 'content_html', 'audio_link', 'video_link', 'external_link', 'has_audio_link', 'has_video_link', 'has_external_link', 'hashtags', 'available_after_content_id', 'expire_in', 'section_id', 'author_id', 'is_featured', 'featured_order', 'course_id', 'module_id', 'is_course', 'likes', 'views', 'comments', 'audio_id', 'order_course', 'category', 'file_url'];

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

    public function audio()
    {
        return $this->hasOne(File::class, 'id', 'audio_id');
    }

    public function thumb_small()
    {
        return $this->hasOne(File::class, 'id', 'thumb_small_id');
    }

    public function thumb_big()
    {
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

    public function content_log()
    {
        return $this->hasMany(ContentLog::class, 'id', 'content_id');
    }

    public function categories()
    {
        return $this->morphToMany(Category::class, 'categorizable');
    }

    // TODO REMOVER //
    public function mostAccessedContent($initialDate, $finalDate, $allDate, $platform_id, $order = 'DESC')
    {
        $sql = "SELECT d.filename, b.title, COUNT(a.content_id) AS amount FROM content_logs a
        INNER JOIN contents b ON a.content_id = b.id
        LEFT JOIN sections c ON b.section_id = c.id
        LEFT JOIN files d ON c.thumb_id = d.id
        WHERE  a.platform_id = '" . $platform_id . "'";

        if ($allDate == 0) {
            $sql .= "AND a.created_at BETWEEN '$initialDate' AND '$finalDate'";
        }

        $sql .= "GROUP BY a.content_id
        ORDER BY amount " . $order . "
        LIMIT 20";

        return $sql;
    }

    // TODO REMOVER //
    public function mostLikedContent($initialDate, $finalDate, $allDate, $platform_id, $order = 'DESC')
    {
        $sql = "SELECT d.filename, b.title , COUNT(a.content_id) AS likes FROM likes a
        INNER JOIN contents b ON a.content_id = b.id
        INNER JOIN sections c ON b.section_id = c.id
        LEFT JOIN files d ON c.thumb_id = d.id
        WHERE  c.platform_id = '" . $platform_id . "'";

        if ($allDate == 0) {
            $sql .= "AND a.created_at BETWEEN '$initialDate' AND '$finalDate'";
        }

        $sql .= "GROUP BY a.content_id
        ORDER BY likes " . $order . "
        LIMIT 20";

        return $sql;
    }

    // TODO REMOVER //
    public function countCommentedContent($initialDate, $finalDate, $allDate, $platform_id, $order = 'DESC')
    {
        $sql = "SELECT d.filename, b.title, COUNT(a.contents_id) AS count_comments FROM comments a
        INNER JOIN contents b ON a.contents_id = b.id
        INNER JOIN sections c ON b.section_id = c.id
        LEFT JOIN files d ON c.thumb_id = d.id
        WHERE a.platform_id = '" . $platform_id . "'";

        if ($allDate == 0) {
            $sql .= "AND a.created_at BETWEEN '$initialDate' AND '$finalDate'";
        }

        $sql .= "GROUP BY a.contents_id
        ORDER BY count_comments " . $order . "
        LIMIT 20";

        return $sql;
    }

    // TODO REMOVER //
    public function contentMostAccessedByAuthor($initialDate, $finalDate, $allDate, $platform_id, $order = 'DESC')
    {
        $sql = "SELECT a.name_author, b.title, SUM(b.views) AS amount FROM authors a
        INNER JOIN contents b ON b.author_id = a.id
        WHERE a.platform_id = '" . $platform_id . "'
        AND a.status = 1 ";

        if ($allDate == 0) {
            $sql .= "AND a.created_at BETWEEN '$initialDate' AND '$finalDate'";
        }

        $sql .= "GROUP BY a.id
        ORDER BY amount  " . $order;

        return $sql;
    }

    static function havePermission($content_id, $subscriber_id)
    {
        $plans = Subscriber::getPlans($subscriber_id);

        $content = self::find($content_id);

        return (
            ($content->section and $content->section->plans()->whereIn('plan_id', $plans)->count() > 0)
            or
            ($content->is_course and $content->course->plans()->whereIn('plan_id', $plans)->count() > 0)
            or Subscriber::checkIfSubscriberHasUnlimitedPlans($subscriber_id)
        );
    }

    static function notAllowed($content_id, $subscriber_id)
    {
        return !self::havePermission($content_id, $subscriber_id);
    }
}
