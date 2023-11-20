<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Dashboard extends Model
{
    public function getSubscribersByStatus($platform_id, $status = NULL, ?string $start = null, ?string $end = null)
    {
        $sql = "SELECT id FROM subscribers
                WHERE platform_id = '$platform_id'";

        if ($status) {
            $sql .= "AND STATUS = '$status' ";
        }

        if ($start) {
            $sql .= " AND created_at >= '{$start}' ";
        }

        if ($end) {
            $sql .= " AND created_at <= '{$end}' ";
        }

        return $sql;
    }

    public function getBillingPrevision($platform_id)
    {
        return "SELECT SUM(b.price) AS revenue FROM subscribers a
                INNER JOIN plans b ON a.plan_id = b.id
                WHERE a.platform_id = '$platform_id'
                AND a.STATUS = 'active'";
    }

    public function getSubscribersGroupByStatus($platform_id, ?string $start = null, ?string $end = null)
    {
        $sql = "SELECT status, DATE(created_at) AS date, COUNT(*) AS amount FROM subscribers
                WHERE platform_id = '$platform_id'
                AND STATUS IN ('active', 'canceled')";

        if ($start) {
            $sql .= " AND created_at >= '{$start}' ";
        }

        if ($end) {
            $sql .= " AND created_at <= '{$end}' ";
        }

        $sql .= "GROUP BY date, status";

        return $sql;
    }

    public function getLastAccess($platform_id)
    {
        return "SELECT a.name, b.filename, a.last_acess FROM subscribers a
                LEFT JOIN files b ON a.thumb_id = b.id
                WHERE platform_id = '$platform_id'
                AND a.last_acess <= NOW()
                ORDER BY a.last_acess DESC
                LIMIT 10";
    }

    public function mostAccessedContent($platform_id)
    {
        return "SELECT
                c.filename, b.title, COUNT(a.content_id) AS amount, b.section_id, b.course_id, d.platform_id, e.platform_id FROM
                content_logs a
                INNER JOIN contents b ON a.content_id = b.id
                INNER JOIN files c ON b.thumb_small_id = c.id
                LEFT JOIN sections d ON b.section_id = d.id
                LEFT JOIN courses e ON b.course_id = e.id
                WHERE a.platform_id = '$platform_id' AND
                (d.platform_id = a.platform_id OR e.platform_id = a.platform_id)
                GROUP BY a.content_id ORDER BY amount DESC LIMIT 10";
    }

    public function getPlanSales($platform_id, ?string $begin = null, ?string $end = null)
    {
        $sql = "SELECT plans.name AS plan_name,
                 COUNT(plans.name) AS sales_count
                FROM `payments`
                LEFT JOIN `payment_recurrence` ON `payment_recurrence`.`payment_id` = `payments`.`id`
                LEFT JOIN `subscribers` ON `subscribers`.`id` = `payments`.`subscriber_id`
                LEFT JOIN `payment_plan` ON `payment_plan`.`payment_id` = `payments`.`id`
                LEFT JOIN `plans` ON `plans`.`id` = `payment_plan`.`plan_id`
                LEFT JOIN `recurrences` ON `recurrences`.`id` = `payment_recurrence`.`recurrence_id`
                LEFT JOIN `platforms` ON `subscribers`.`platform_id` = `platforms`.`id`
                LEFT JOIN `clients` ON `platforms`.`customer_id` = `clients`.`id`
                WHERE subscribers.platform_id = '$platform_id'
                AND payments.status = 'paid' ";

        if ($begin) {
            $sql .= " AND payments.payment_date >= '{$begin}' ";
        }
        if ($end) {
            $sql .= " AND payments.payment_date <= '{$end}' ";
        }

        $sql .= "GROUP BY (plans.name)";
        return DB::select($sql);
    }

    public function getCoursesSales($platform_id)
    {
        return "SELECT a.name, count(b.subscriber_id) AS amount FROM courses a
                LEFT JOIN course_subscribers b ON b.course_id = a.id
                WHERE a.platform_id = '$platform_id'
                AND a.active = 1
                GROUP BY b.course_id";
    }

    public function getOnlineUsers($platform_id)
    {
        return "SELECT a.name, b.filename, a.email FROM subscribers a
                LEFT JOIN files b ON a.thumb_id = b.id
                WHERE a.platform_id = '$platform_id'
                AND a.login > a.last_acess";
    }

    public function getNewsSubscribers($platform_id, ?string $start = null, ?string $end = null, $status_payment = null, $limit = null)
    {
       $sql = "SELECT DISTINCT(subscribers.id), subscribers.name, subscribers.created_at, files.filename, payments.status
                FROM `subscribers`
                INNER JOIN `files` ON `subscribers`.`thumb_id` = `files`.`id`
                INNER JOIN `payments` ON `subscribers`.`id` = `payments`.`subscriber_id`
                WHERE 
                subscribers.platform_id = '$platform_id'
                AND subscribers.status = 'active'
                ";

        if ($start) {
            $sql .= " AND subscribers.created_at >= '{$start}' ";
        }

        if ($end) {
            $sql .= " AND subscribers.created_at <= '{$end}' ";
        }

        if ($status_payment) {
            $sql .= " AND payments.status <= '{$status_payment}' ";
        }

        if ($limit) {
            $sql .= " LIMIT {$limit}";
        }

        return $sql;
    }
}
