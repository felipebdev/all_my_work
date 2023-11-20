<?php

namespace App;

use App\Content;
use App\PlatformUser;
use App\Subscriber;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Comment extends Model
{
    protected $fillable = ['platform_id', 'subscriber_id', 'contents_id', 'text', 'comment_id', 'id_comment_sub', 'like', 'views', 'approved', 'subscriber_type', 'platform_user_id'];

    public function commentable()
    {
        return $this->morphTo();
    }

    public function comment()
    {
        return $this->belongsto(Comment::class);
    }

    public function content()
    {
        return $this->belongsto(Content::class, 'contents_id', 'id');
    }

    public function subscriber()
    {
        return $this->belongsto(Subscriber::class);
    }


    public function platform_user()
    {
        return $this->belongsto(PlatformUser::class);
    }


    static function getData($platform_id)
    {
        //IF(subscriber_type = 'subscriber', (SELECT CONCAT(name, ',', 'id') FROM subscribers WHERE id = a.subscriber_id), 'cliente') author

        $comments = DB::select("SELECT * FROM comments a INNER JOIN contents b ON a.contents_id = b.id  WHERE a.platform_id=:platform_id", [
                     ':platform_id' => $platform_id
                 ]);

        return $comments;
    }

    public function getAllComments($plataform_id, $approved){
        $sql = "SELECT a.id, a.text, a.created_at, a.approved, b.name, b.email, d.name AS section,
                       g.filename AS image, e.name_author AS author, f.name AS course, c.title,
                       (select filename from files where id = b.thumb_id) as avatar
                FROM comments a
                INNER JOIN subscribers b ON a.subscriber_id = b.id
                INNER JOIN contents c ON a.contents_id = c.id
                INNER JOIN authors e ON c.author_id = e.id
                LEFT JOIN sections d ON c.section_id = d.id
                LEFT JOIN courses f ON c.course_id = f.id
                LEFT JOIN files g ON c.thumb_small_id = g.id
                WHERE 
                ((c.is_course = '1' AND f.platform_id = '$plataform_id') OR d.platform_id = '$plataform_id')
                AND a.approved = '$approved'
                AND a.id_comment_sub IS NULL
                ORDER BY b.name ASC";
        return DB::select($sql);
    }
}

