<?php

namespace App\Http\Controllers\Reports;

use App\AccessLog;
use App\Content;
use App\ContentLog;
use App\ContentSubscriber;
use App\Http\Controllers\Controller;
use App\Course;
use App\Subscriber;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CourseReportController extends Controller
{
    private $accessLog;
    private $contentLog;
    private $content;
    private $subscriber;
    private $course;
    private $contentSubscriber;

    public function __construct(
        AccessLog $accessLog,
        ContentLog $contentLog,
        Content $content,
        Subscriber $subscriber,
        Course $course,
        ContentSubscriber $contentSubscriber
    )
    {
        $this->accessLog = $accessLog;
        $this->contentLog = $contentLog;
        $this->content = $content;
        $this->subscriber = $subscriber;
        $this->course = $course;
        $this->contentSubscriber = $contentSubscriber;
    }

    public function index()
    {

        $initialDate = date('Y-m-d', strtotime(date("Y-m-d") . "-1 month")) . ' 00:00:00';
        $finalDate = date('Y-m-d', strtotime(date('Y-m-d'))) . ' 23:59:59';
        $datePeriod = date("d/m/Y", strtotime($initialDate)) . ' - ' . date("d/m/Y", strtotime($finalDate));
        $search = ['period' => $datePeriod];
        $coursesSQL = $this->course->getCourseByPlatformId(Auth::user()->platform_id);
        $courses = DB::select($coursesSQL);

        $dataIni = date('d/m/Y', strtotime('-1 month'));
        $dataFin = date('d/m/Y');

        $platform_id = Auth::user()->platform_id;
        $list_courses = $this->course->where('platform_id', Auth::user()->platform_id)
            ->orderBy('name', 'ASC')
            ->get(['id', 'name']);

        return view('reports.course.course', compact('search', 'courses', 'list_courses', 'dataIni', 'dataFin'));
    }

    public function convertPeriod($period)
    {
        $periods = explode(' - ', $period);
        $startPeriod = $periods[0];
        $startPeriod = explode('/', $startPeriod);
        $endPeriod = $periods[1];
        $endPeriod = explode('/', $endPeriod);
        return [$startPeriod[2] . '-' . $startPeriod[1] . '-' . $startPeriod[0] . ' 00:00:00', $endPeriod[2] . '-' . $endPeriod[1] . '-' . $endPeriod[0] . ' 23:59:59'];
    }

    public function getMostViewedCourses()
    {
        try {
            $sql = $this->course->getMostViewedCourses(Auth::user()->platform_id);
            $getMostViewedCourses = DB::select($sql);
            $labels = $data = [];
            foreach ($getMostViewedCourses as $item) {
                array_push($labels, ucfirst(mb_strtolower($item->name)));
                array_push($data, $item->views);
            }
            return response()->json([
                'labels' => $labels,
                'data' => $data,
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => true, 'response' => $e->getMessage()]);
        }
    }

    public function getMostViewedCourseByDayWeek(Request $request)
    {
        try {
            if ($request->course) {
                $course_id = $request->course;
            } else {
                $course_id = $this->course->where('platform_id', Auth::user()->platform_id)->first();
                $course_id = $course_id->id;
            }

            $sql = $this->course->mostViewedCourseByDayWeek(Auth::user()->platform_id, $course_id);
            $mostViewedCourseByDayWeek = DB::select($sql);
            $data = [];
            $labels = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab'];
            $days = ['1', '2', '3', '4', '5', '6', '7'];

            foreach ($days as $day) {
                $hasAccessDay = false;
                foreach ($mostViewedCourseByDayWeek as $item) {
                    if ($day == $item->day) {
                        array_push($data, $item->amount);
                        $hasAccessDay = true;
                    }
                }
                if (!$hasAccessDay) {
                    array_push($data, 0);
                }
            }
            return response()->json([
                'labels' => $labels,
                'data' => $data,
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => true, 'response' => $e->getMessage()]);
        }
    }

    public function getSubscriberCourses()
    {
        try {
            $sqlWithoutCourse = $this->course->getSubscriberCourse(Auth::user()->platform_id, 'sem_curso');
            $subscriberWithoutCourse = DB::select($sqlWithoutCourse);

            $sqlWithCourse = $this->course->getSubscriberCourse(Auth::user()->platform_id, 'com_curso');
            $subscriberWithCourse = DB::select($sqlWithCourse);

            $labels = ['Em curso', 'Sem curso'];
            return response()->json([
                'labels' => $labels,
                'status' => [
                    ['value' => count($subscriberWithCourse), 'name' => $labels[0]],
                    ['value' => count($subscriberWithoutCourse), 'name' => $labels[1]]
                ],
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => true, 'response' => $e->getMessage()]);
        }
    }

    public function getSubscriberWithCourses()
    {
        try {
            $sqlWithCourse = $this->course->getSubscriberCourse(Auth::user()->platform_id, 'com_curso');
            $subscriberWithCourse = DB::select($sqlWithCourse);
            return response()->json([
                'data' => $subscriberWithCourse,
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => true, 'response' => $e->getMessage()]);
        }
    }

    public function getSubscriberWithoutCourses()
    {
        try {
            $sqlWithoutCourse = $this->course->getSubscriberCourse(Auth::user()->platform_id, 'sem_curso');
            $subscriberWithoutCourse = DB::select($sqlWithoutCourse);
            return response()->json([
                'data' => $subscriberWithoutCourse,
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => true, 'response' => $e->getMessage()]);
        }
    }

    public function getSubscriberByCourse()
    {
        try {
            $subscribers = $this->subscriber
                ->select(DB::raw('count(subscribers.id) as quantity, courses.name as course'))
                ->join('course_subscribers', 'subscribers.id', '=', 'course_subscribers.subscriber_id')
                ->join('courses', 'course_subscribers.course_id', '=', 'courses.id')
                ->where(['subscribers.platform_id' => Auth::user()->platform_id])
                ->where(['subscribers.status' => 'active'])
                ->groupBy('course')
                ->get();
            $labels = $data = [];
            foreach ($subscribers as $subscriber){
                array_push($labels, ucfirst(mb_strtolower($subscriber->course)));
                array_push($data, $subscriber->quantity);
            }
            return response()->json([
                'labels' => $labels,
                'data' => $data,
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => true, 'response' => $e->getMessage()]);
        }
    }
}
