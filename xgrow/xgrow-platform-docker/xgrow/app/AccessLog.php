<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccessLog extends Model
{
    protected $fillable = [
        'type', 'description', 'user_id', 'user_type', 'platform_id', 'ip', 'browser_type', 'device_type'
    ];


    static function searchDevice($user_agent)
    {
        $iphone = strpos($user_agent, "iPhone");
        $ipad = strpos($user_agent, "iPad");
        $android = strpos($user_agent, "Android");
        $palmpre = strpos($user_agent, "webOS");
        $berry = strpos($user_agent, "BlackBerry");
        $ipod = strpos($user_agent, "iPod");
        $symbian = strpos($user_agent, "Symbian");
        $windowsphone = strpos($user_agent, "Windows Phone");

        return ($iphone || $ipad || $android || $palmpre || $ipod || $berry || $symbian || $windowsphone == true) ? "mobile" : "computer";
    }

    static function searchBrowser($user_agent)
    {
        $t = strtolower($user_agent);

        if (strpos($t, 'opera') || strpos($t, 'opr/')) return 'Opera';
        elseif (strpos($t, 'edge')) return 'Edge';
        elseif (strpos($t, 'chrome')) return 'Chrome';
        elseif (strpos($t, 'safari')) return 'Safari';
        elseif (strpos($t, 'firefox')) return 'Firefox';
        elseif (strpos($t, 'PostmanRuntime')) return 'Postman';
        elseif (strpos($t, 'msie') || strpos($t, 'trident/7')) return 'Internet Explorer';

        return 'API - Undefined';
    }

    public function platform_user()
    {
        return $this->belongsTo(PlatformUser::class, 'user_id');
    }

    public function subscriber()
    {
        return $this->belongsTo(Subscriber::class, 'user_id');
    }

    public function hitsPerDay($initialDate, $finalDate, $allDate, $platform_id)
    {
        $sql = "SELECT id, DATE(created_at) AS date, COUNT(*) AS amount FROM access_logs
                WHERE platform_id = '" . $platform_id . "'
                AND type = 'LOGIN'";
        if ($allDate == 0) {
            $sql .= "AND created_at BETWEEN '$initialDate' AND '$finalDate'";
        }
        $sql .= "GROUP BY DATE(created_at)
                 ORDER BY DATE(created_at) ASC";
        return $sql;
    }

    public function hitsPerHour($initialDate, $finalDate, $allDate, $platform_id)
    {
        $sql = "SELECT id, HOUR(created_at) AS time, COUNT(*) AS amount FROM access_logs
                WHERE platform_id = '" . $platform_id . "'
                AND type = 'LOGIN'";
        if ($allDate == 0) {
            $sql .= "AND created_at BETWEEN '$initialDate' AND '$finalDate'";
        }
        $sql .= "GROUP BY HOUR(created_at)
                 ORDER BY TIME(created_at) ASC";
        return $sql;
    }

    public function hitsPerDayWeek($initialDate, $finalDate, $allDate, $platform_id)
    {
        $sql = "SELECT id, DAYOFWEEK(created_at) AS day, COUNT(*) AS amount FROM access_logs
                WHERE platform_id = '" . $platform_id . "'
                AND type = 'LOGIN'";
        if ($allDate == 0) {
            $sql .= "AND created_at BETWEEN '$initialDate' AND '$finalDate'";
        }
        $sql .= "GROUP BY DAYOFWEEK(created_at)
                 ORDER BY DAYOFWEEK(created_at) ASC";
        return $sql;
    }

    public function byGender($initialDate, $finalDate, $allDate, $platform_id, $gender)
    {
        $sql = "SELECT DISTINCT s.id, s.gender FROM access_logs a
                INNER JOIN subscribers s
                WHERE a.user_id = s.id
                AND a.platform_id = '" . $platform_id . "'
                AND a.type = 'LOGIN'
                AND a.user_type = 'subscribers'
                AND s.gender = '" . $gender . "'";
        if ($allDate == 0) {
            $sql .= "AND a.created_at BETWEEN '$initialDate' AND '$finalDate'";
        }
        return $sql;
    }

    public function byAgeGender($initialDate, $finalDate, $allDate, $platform_id, $gender)
    {
        $sql = "SELECT DISTINCT s.id, s.name, s.gender, s.birthday,
                YEAR(CURDATE()) - YEAR(s.birthday) - IF(STR_TO_DATE(CONCAT(YEAR(CURDATE()), '-', MONTH(s.birthday), '-', DAY(s.birthday)) ,'%Y-%c-%e') > CURDATE(), 1, 0)AS age
                FROM access_logs a INNER JOIN subscribers s
                WHERE a.user_id = s.id
                AND a.platform_id = '" . $platform_id . "'
                AND a.type = 'LOGIN'
                AND a.user_type = 'subscribers'
                AND s.gender = '" . $gender . "'";
        if ($allDate == 0) {
            $sql .= "AND a.created_at BETWEEN '$initialDate' AND '$finalDate'";
        }
        return $sql;
    }

    public function hitsByLocation($initialDate, $finalDate, $allDate, $platform_id)
    {
        $sql = "SELECT DISTINCT s.id, s.address_city, COUNT(s.address_city) AS access
                FROM access_logs a INNER JOIN subscribers s
                WHERE a.user_id = s.id
                AND a.platform_id = '" . $platform_id . "'
                AND a.type = 'LOGIN'
                AND a.user_type = 'subscribers'";
        if ($allDate == 0) {
            $sql .= "AND a.created_at BETWEEN '$initialDate' AND '$finalDate'";
        }
        $sql .= "GROUP	BY s.address_city
                 ORDER BY s.address_city ASC";
        return $sql;
    }

    //SELECT SEC_TO_TIME(avg(time_access)) AS avg_time_access, COUNT('time_access') AS registers FROM (SELECT id, TIMESTAMPDIFF(SECOND, created_at, finished_at) AS time_access FROM content_logs
    //WHERE platform_id = '7658c4c7-92eb-4d59-8001-a4dd638d2e57'
    //AND user_type = 'subscribers'
    //AND finished_at IS NOT NULL
    //AND created_at BETWEEN '2020-11-18 00:00:00' AND '2020-12-18 23:59:59'
    //ORDER BY DATE(created_at) ASC) AS total

    public function avgAccessTime($initialDate, $finalDate, $allDate, $platform_id)
    {
    /* SELECT *, TIMESTAMPDIFF(SECOND, created_at, finished_at) AS time_access FROM content_logs
       WHERE platform_id = '" . $platform_id . "'
       AND user_type = 'subscribers'
       AND finished_at IS NOT NULL
       AND created_at BETWEEN '$initialDate' AND '$finalDate'
       GROUP BY DATE(created_at)
       ORDER BY DATE(created_at) ASC";*/

        $sql = "SELECT SEC_TO_TIME(avg(time_access)) AS avg_time_access, COUNT('time_access') AS registers FROM (SELECT id, TIMESTAMPDIFF(SECOND, created_at, finished_at) AS time_access FROM content_logs
                WHERE platform_id = '" . $platform_id . "'
                AND user_type = 'subscribers'
                AND finished_at IS NOT NULL ";
        if ($allDate == 0) {
            $sql .= "AND created_at BETWEEN '$initialDate' AND '$finalDate'";
        }
        $sql .= "ORDER BY DATE(created_at) ASC) AS total";

        return $sql;
    }
}
