<?php

namespace App\Http\Controllers\Reports;

use App\Content;
use App\Course;
use App\CourseSubscriber;
use App\Helpers\CollectionHelper;
use App\Http\Controllers\Controller;
use App\Http\Traits\CustomResponseTrait;
use App\Services\LAService;
use App\Services\Reports\ProgressReportService;
use App\Subscriber;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ProgressReportController extends Controller
{

    private $progressReportService;

    use CustomResponseTrait;


    public function __construct(ProgressReportService $progressReportService)
    {
        $this->progressReportService = $progressReportService;
    }

    /**
     * Return the LA Service connection
     * @return LAService
     */
    private function laService(): LAService
    {
        $platform_id = Auth::user()->platform_id;
        $user_id = Auth::user()->id;
        return new LAService($platform_id, (int)$user_id);
    }

    /**
     * Initial Progress
     * @return View|Factory
     * @throws BindingResolutionException
     */
    public function index()
    {
        return view('reports.progress.index');
    }

    /**
     * Simplified Progress
     * @return View|Factory
     * @throws BindingResolutionException
     */
    public function simplifiedProgress()
    {
        return view('reports.simplified-progress.index');
    }

    /**
     * Get all active courses by platform
     * @param Request $request
     * @return JsonResponse|void
     */
    public function getSubscriberSimplifiedProgress(Request $request)
    {
        $subscriber = $request->input('subscriber');
        $course = $request->input('course');

        $laService = $this->laService();

        try {
            $res = $laService->get(
                '/reports/complete-content-progress',
                [
                    "subscriberIds" => [$subscriber],
                    "courseIds" => [$course]
                ]
            );

            $data = json_decode(json_encode($res), true);

            return $this->customJsonResponse('Dados carregados com sucesso.', 200, $data);
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400);
        }
    }

    /**
     * Get data access from LA API
     * @param Request $request
     * @return never
     */
    public function getProgressAPI(Request $request)
    {
        try {
            $offset = $request->input('offset') ?? 25;
            $subscribers = $this->getSubscribersByCourse($request->course, $request->page, $request->offset);
            Log::info('Progresso Lista Alunos', ['data' => $subscribers]);
            $data = [];

            if ($request->course) {
                $data = (new ProgressReportService)->getSimpleContentProgress($request->course, $subscribers);
                $data = collect($data);

                // Need create specific filters for search - FILTERS
                $filterSearch = $request->search ?? null;
                $filterCourse = $request->course ?? null;
                $filterFirstAccess = $request->firstAccess ?? null;
                $filterLastAccess = $request->lastAccess ?? null;

                if ($filterSearch) {
                    $data = $data->filter(function ($res) use ($filterSearch) {
                        return str_contains(strtolower($res->userName), strtolower($filterSearch));
                    });
                }

                if ($filterCourse) {
                    $data = $data->filter(function ($res) use ($filterCourse) {
                        if ($res->courseId == (int)$filterCourse) return $res;
                    });
                }

                if ($filterFirstAccess) {
                    $data = $data->filter(function ($res) use ($filterFirstAccess) {
                        // "firstAccess": "07/09/2021 05:40",
                        /** Date returned by API */
                        $firstAccess = explode(' ', $res->firstAccess);
                        $firstAccess = $firstAccess[0];

                        /** Date returned by filter */
                        $period = explode(' - ', $filterFirstAccess);
                        $startPeriod = $period[0];
                        $endPeriod = $period[1];

                        /** Convert to Carbon features */
                        $firstAccess = Carbon::createFromFormat('d/m/Y', $firstAccess);
                        $startPeriod = Carbon::createFromFormat('d/m/Y', $startPeriod);
                        $endPeriod = Carbon::createFromFormat('d/m/Y', $endPeriod);

                        /** Return if date between first and end period */
                        if ($firstAccess->between($startPeriod, $endPeriod)) return $res;
                    });
                }

                if ($filterLastAccess) {
                    $data = $data->filter(function ($res) use ($filterLastAccess) {
                        // "firstAccess": "07/09/2021 05:40",
                        /** Date returned by API */
                        $lastAccess = explode(' ', $res->lastAccess);
                        $lastAccess = $lastAccess[0];

                        /** Date returned by filter */
                        $period = explode(' - ', $filterLastAccess);
                        $startPeriod = $period[0];
                        $endPeriod = $period[1];

                        /** Convert to Carbon features */
                        $lastAccess = Carbon::createFromFormat('d/m/Y', $lastAccess);
                        $startPeriod = Carbon::createFromFormat('d/m/Y', $startPeriod);
                        $endPeriod = Carbon::createFromFormat('d/m/Y', $endPeriod);

                        /** Return if date between first and end period */
                        if ($lastAccess->between($startPeriod, $endPeriod)) return $res;
                    });
                }

                $data = CollectionHelper::paginate($data, $offset);
            }

            return $this->customJsonResponse('', 200, [
                'data' => ($request->course) ? $data : []
            ]);
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400);
        }
    }

    /**
     * Get all active courses by platform
     * @param Request $request
     * @return JsonResponse|void
     */
    public function getCoursesByPlatform()
    {
        try {
            $courses = Course::select(['id', 'name'])
                ->where('platform_id', Auth::user()->platform_id)
                ->where('active', true)->get();

            $courses = collect($courses)->pluck('name', 'id');

            return $this->customJsonResponse('', 200, ['data' => $courses]);
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400);
        }
    }

    /**
     * Select qtd of subscribers for search on LA API
     * @param mixed $courseId
     * @param int $page
     * @param int $limit
     * @return string|false|JsonResponse
     * @throws BindingResolutionException
     */
    private function getSubscribersByCourse($courseId, $page = 1, $limit = 25)
    {
        try {
            $offset = ($page - 1) * $limit;
            $subscribers = Subscriber::select(['subscribers.id'])
                ->leftJoin('course_subscribers', 'course_subscribers.subscriber_id', '=', 'subscribers.id')
                ->where('subscribers.platform_id', Auth::user()->platform_id)
                ->where('course_subscribers.course_id', $courseId)
                ->where('subscribers.status', 'active')
                // ->offset($offset)
                // ->limit($limit)
                ->get();

            return $subscribers->pluck('id')->toArray();
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400);
        }
    }

    /**
     * Convert Data Range Plugin data to English date (new)
     * @param $period
     * @return string[]
     */
    public function convertPeriodToEn($period): array
    {
        $periods = explode(' - ', $period);
        $startPeriod = $periods[0];
        $startPeriod = explode('/', $startPeriod);
        $endPeriod = $periods[1];
        $endPeriod = explode('/', $endPeriod);

        return [
            'initialDate' => $startPeriod[2] . '-' . $startPeriod[1] . '-' . $startPeriod[0],
            'finalDate' => $endPeriod[2] . '-' . $endPeriod[1] . '-' . $endPeriod[0]
        ];
    }

    /** Get subscriber for report
     * @param Request $request
     * @return JsonResponse
     */
    public function getSubscribers(Request $request): JsonResponse
    {
        try {
            $offset = $request->input('offset') ?? 25;
            $term = $request->input('term') ?? null;
            $course = $request->input('course') ?? null;

            $subscribers = $this->progressReportService->getSubscribersForProgess($term, $course)->get();
            $data = CollectionHelper::paginate($subscribers, $offset);

            return $this->customJsonResponse('Dados carregados com sucesso.', 200, [$data]);
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400);
        }
    }
}
