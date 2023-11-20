<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Module extends Model
{
    protected $fillable = [
        'name', 'description', 'enable_comments', 'allow_to_order_sequence', 'order', 'course_id', 'diagram'
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function contents()
    {
        return $this->hasMany(Content::class);
    }


    public function classes($course_id, $subscriber_id)
    {

        $course = Course::find($course_id);

        $modules = $course->modules()->where('course_id', $course_id)->orderBy('order', 'ASC');

        $modules = $modules->get();

        $order = 1;
        $order_course = 1;

        foreach ($modules as $key => $module) {
            $content = new Content;
            $contents = $content->where('module_id', $module->id)
                ->where('published', 1)
                ->where('is_course', 1)
                ->where('course_id', '<>', 0)
                ->select('id', 'title')
                ->orderBy('featured_order', 'ASC')->get();

            $modules[$key]['contents'] = $contents;
            $assisted_quantities = 0;
            foreach ($contents as $index => $content) {
                $modules[$key]['contents'][$index]['order'] = $order;
                $modules[$key]['contents'][$index]['published'] = $this->checkIfPublished($content->id, $subscriber_id, $order_course);
                $order_course++;
                $watcheds = $content->subscribers()
                    ->where('subscriber_id', $subscriber_id)
                    ->where('concluded', 1)
                    ->count();

                $modules[$key]['contents'][$index]['subscribers'] = $watcheds;

                if ($watcheds > 0)
                    $assisted_quantities++;

                $order++;
            }

            $modules[$key]['assisted_quantities'] = $assisted_quantities;
        }

        return $modules;
    }

    public function checkIfPublished($content_id, $subscriber_id, $order_in_course = 0)
    {
        $content = Content::find($content_id);
        $course = Course::find($content->course_id);
        $visibled = 1;

        switch ($course->delivery_date) {
            case 1:
                $reference_date = $course->started_at;
                break;

            case 2:
                $reference_date = $course->delivered_at;
                break;

            default:
                $reference_date = $this->getDateCourseStartClass($course, $subscriber_id);
                break;
        }

        $form_delivery = $course->form_delivery;
        $delivery_model = $course->delivery_model;
        $frequency = $course->frequency;

        $reference_date = strtotime($reference_date);
        $current_date = strtotime(date('Y-m-d'));

        if ($order_in_course == 0) {
            $order_in_course = $this->getOrderCourse($content_id);
        }

        $order = ($delivery_model == 1) ? $content->module->order : $order_in_course;

        $y = date('Y', $reference_date);
        $m = date('m', $reference_date);
        $d = date('d', $reference_date) + (($order - 1) * $frequency);

        $reference_date = mktime(0, 0, 0, $m, $d, $y);

        if ($form_delivery == 2) {
            if ($reference_date > $current_date) {
                $visibled = 0;
            }
        }
        // $visibled = date('Y-m-d', $reference_date);
        return [
            'visibled' => $visibled,
            'available_in' => date('d/m/Y', $reference_date),
            'order_in_course' => $order_in_course,
        ];
    }

    private function getDateCourseStartClass($course, $subscriber_id)
    {

        $date_start = date('Y-m-d');

        $start_course = $course->subscribers()->where('subscriber_id', $subscriber_id)->first();

        if ($start_course) {
            $date_start = date('Y-m-d', strtotime($start_course->created_at));
        }


        if ($course->paid == 1) {
            $payment = $course->payments()->where('subscriber_id', $subscriber_id)
                ->where('status', 'approved')->first();
            if ($payment)
                $date_start = date('Y-m-d', strtotime($payment->credit_authorized_at));
        }

        return $date_start;
    }


    /**
     * retorna a ordem de uma aula
     * @param int $content_id
     * @return int
     */
    public function getOrderCourse($content_id)
    {
        $content = Content::find($content_id);
        $course_id = $content->course_id;
        $module_id = $content->module->id;
        $module_order = $content->module->order;
        $featured_order = $content->featured_order;

        $result1 = DB::select("SELECT COUNT(*) count FROM contents c INNER JOIN modules m ON c.module_id = m.id WHERE
			m.order <= :module_order AND c.featured_order <= :featured_order AND m.id=:module_id
			 AND c.course_id = :course_id AND c.published = 1", [
            ':course_id' => $course_id,
            ':module_order' => $module_order,
            ':module_id' => $module_id,
            ':featured_order' => $featured_order,
        ]);

        //mesmo módulo
        $count1 = $result1[0]->count;

        $result2 = DB::select("SELECT COUNT(*) count FROM contents c INNER JOIN modules m ON c.module_id = m.id WHERE
			m.order < :module_order AND c.course_id = :course_id AND c.published = 1", [
            ':course_id' => $course_id,
            ':module_order' => $module_order,
        ]);

        //módulos anteriores
        $count2 = $result2[0]->count;

        $total = $count1 + $count2;

        return $total;
    }

    public function class()
    {
        return $this->hasMany(Content::class);
    }
}
