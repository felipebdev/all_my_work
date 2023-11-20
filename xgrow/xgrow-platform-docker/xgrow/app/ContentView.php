<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContentView extends Model
{
    protected $fillable = ['id', 'content_id', 'created_at', 'updated_at'];

    public function content()
    {
        return $this->hasOne(Content::class, 'id', 'content_id');
    }

    // TODO REMOVER //
    public function getTotalViewsContentByAuthor($initialDate, $finalDate, $allDate, $platform_id)
    {
        $sql = "SELECT c.name_author, count(c.name_author) AS total FROM content_views a
                INNER JOIN contents b ON a.content_id = b.id
                INNER JOIN authors c ON b.author_id = c.id
                WHERE c.platform_id = '$platform_id'";
        if ($allDate == 0) {
            $sql .= "AND a.created_at BETWEEN '$initialDate' AND '$finalDate'";
        }
        $sql .= "GROUP BY c.name_author";
        return $sql;
    }
}
