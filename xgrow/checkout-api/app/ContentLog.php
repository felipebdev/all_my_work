<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContentLog extends Model
{
    protected $fillable = [
        'route', 'user_id', 'user_type', 'platform_id', 'ip', 'section_id', 'section_key', 'content_id', 'course_id'
    ];

    public function content()
    {
        return $this->belongsTo(Content::class, 'content_id');
    }

    public function mostAccessedSection($initialDate, $finalDate, $allDate, $platform_id)
    {
        $sql = "SELECT a.name, COUNT(b.section_key) AS amount FROM sections a
        INNER JOIN content_logs b
        WHERE a.section_key = b.section_key
        AND a.platform_id = '" . $platform_id . "'";

        if ($allDate == 0) {
            $sql .= "AND a.created_at BETWEEN '$initialDate' AND '$finalDate'";
        }

        $sql .= "GROUP BY a.section_key
        ORDER BY amount DESC
        LIMIT 20";
        return $sql;
    }

    public function updateContentLog($user_id){
        return "UPDATE content_logs SET finished_at = NOW() WHERE finished_at = NULL AND user_id = $user_id";
    }

}
