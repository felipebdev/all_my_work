<?php

namespace App\Http\Controllers\Reports;

use App\AccessLog;
use App\Content;
use App\ContentLog;
use App\ContentSubscriber;
use App\Course;
use App\Http\Controllers\Controller;
use App\Http\Traits\CustomResponseTrait;
use App\Platform;
use App\Services\Reports\AccessReportService;
use App\Subscriber;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AccessReportController extends Controller
{
    use CustomResponseTrait;

    private $accessLog;
    private $contentLog;
    private $content;
    private $subscriber;
    private $course;
    private $contentSubscriber;
    private $platform;
    private $redisTime = 3600 * 1;

    public function __construct(
        AccessLog $accessLog,
        ContentLog $contentLog,
        Content $content,
        Subscriber $subscriber,
        Course $course,
        ContentSubscriber $contentSubscriber,
        Platform $platform
    ) {
        $this->accessLog = $accessLog;
        $this->contentLog = $contentLog;
        $this->content = $content;
        $this->subscriber = $subscriber;
        $this->course = $course;
        $this->contentSubscriber = $contentSubscriber;
        $this->platform = $platform;
    }


    /**
     * Init first API Cache
     * @param $period
     */
    public function cacheApi($type, $period)
    {
        $platformId = Auth::user()->platform_id;
        $period = $this->convertPeriodToEn($period);
        $initialDate = $period['initialDate'];
        $finalDate = $period['finalDate'];
        $data = '';

        if ($type == 'getLogs') {
            $data = Cache::store('redis')->remember(
                "reports:access:getLogs:{$platformId}:{$initialDate}-{$finalDate}",
                $this->redisTime,
                function () use ($initialDate, $finalDate) {
                    return (new AccessReportService())->getLogs($initialDate, $finalDate, 'logIn');
                }
            );
        }

        return $data;
    }

    public function index(Request $request)
    {
        $initialDate = Carbon::now()->subMonths(1)->format('d/m/Y');
        $finalDate = Carbon::now()->format('d/m/Y');
        $period = $initialDate . ' - ' . $finalDate;
        $search = ['period' => $period];
        return view('reports.access.access', compact('search'));
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

    /**
     * Get quantity hits by hour
     * @param Request $request
     * @return JsonResponse
     */
    public function hitsHourDay(Request $request): JsonResponse
    {
        try {
            $data = $this->cacheApi('getLogs', $request->period);
            $collection = collect($data->data);
            $counted = $collection->countBy(function ($value) {
                return (int)Carbon::make($value->createdAt)->format('H');
            });

            $labels = $values = [];
            foreach ($counted->sortKeys()->all() as $label => $key) {
                $values[] = $key;
                $labels[] = $label . ':00';
            }

            return $this->customJsonResponse('Dados carregados com sucesso', 200, ['labels' => $labels, 'data' => $values]);
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400);
        }
    }

    /**
     * Get quantity hits by day
     * @param Request $request
     * @return JsonResponse
     */
    public function hitsPerDay(Request $request): JsonResponse
    {
        try {
            $data = $this->cacheApi('getLogs', $request->period);
            $collection = collect($data->data);
            $counted = $collection->countBy(function ($value) {
                return Carbon::make($value->createdAt)->format('Y-m-d');
            });
            $labels = $data = [];
            foreach ($counted->sortKeys()->all() as $label => $key) {
                $labels[] = Carbon::make($label)->format('d/m/Y');
                $data[] = $key;
            }

            return $this->customJsonResponse('Dados carregados com sucesso', 200, ['labels' => $labels, 'data' => $data]);
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400);
        }
    }

    /**
     * Get quantity hits by day in a week
     * @param Request $request
     * @return JsonResponse
     */
    public function hitsDayWeek(Request $request): JsonResponse
    {
        try {
            $data = $this->cacheApi('getLogs', $request->period);
            $collection = collect($data->data);
            $counted = $collection->countBy(function ($value) {
                return Carbon::make($value->createdAt)->dayOfWeek;
            });

            $data = [];
            $labels = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab'];
            $res = $counted->sortKeys()->all();

            $i = 0;
            foreach ($labels as $l) {
                (!isset($res[$i])) ? $data[] = 0 : $data[] = $res[$i];
                $i++;
            }

            return $this->customJsonResponse('Dados carregados com sucesso', 200, ['labels' => $labels, 'data' => $data]);
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400);
        }
    }

    /**
     * Get data access by age/gender
     * @param Request $request
     * @return JsonResponse|void
     */
    public function ageGender(Request $request)
    {
        try {
            $data = $this->cacheApi('getLogs', $request->period);

            $uniqueUsers = collect($data->data)->unique('userId')->map(function ($item) {
                return $item->userId;
            });

            $users = Subscriber::select(['name', 'gender', 'birthday'])->whereIn('id', $uniqueUsers->flatten())->get();

            $users = collect($users)->map(function ($user) {
                return [
                    'name' => $user->name,
                    'gender' => $user->gender,
                    'birthday' => date_diff(date_create($user->birthday), date_create(date("d-m-Y")))->format('%y'),
                ];
            });

            $filteredMale = $users->filter(function ($user) {
                return $user['gender'] == "male";
            });

            $filteredFemale = $users->filter(function ($user) {
                return $user['gender'] == "female";
            });

            $filteredNull = $users->filter(function ($user) {
                return $user['gender'] == "" || $user['gender'] == null;
            });

            $labels = ['+65', '55-64', '45-54', '35-44', '25-34', '18-24', '13-17'];

            $res = [
                'labels' => $labels,
                'dataFemale' => $this->ageRange($filteredFemale),
                'dataMale' => $this->ageRange($filteredMale),
                'dataUndefined' => $this->ageRange($filteredNull),

            ];

            return $this->customJsonResponse('Dados carregados com sucesso', 200, $res);
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400);
        }
    }

    /**
     * Make calculations for generate age/gender
     * @param mixed $data
     * @return int[]
     */
    public function ageRange($data)
    {
        $dataArray = [0, 0, 0, 0, 0, 0, 0];
        foreach ($data as $age) {
            if ((int)$age['birthday'] > 12) {
                if ((int)$age['birthday'] < 18) {
                    $dataArray[0]++;
                } elseif ((int)$age['birthday'] < 25) {
                    $dataArray[1]++;
                } elseif ((int)$age['birthday'] < 35) {
                    $dataArray[2]++;
                } elseif ((int)$age['birthday'] < 45) {
                    $dataArray[3]++;
                } elseif ((int)$age['birthday'] < 55) {
                    $dataArray[4]++;
                } elseif ((int)$age['birthday'] < 65) {
                    $dataArray[5]++;
                } else {
                    $dataArray[6]++;
                }
            }
        }
        return array_reverse($dataArray);
    }

    /**
     * Get access by Gender
     * @param Request $request
     * @return JsonResponse|void
     */
    public function gender(Request $request)
    {
        try {
            $data = $this->cacheApi('getLogs', $request->period);
            $uniqueUsers = collect($data->data)->unique('userId')->map(function ($item) {
                return $item->userId;
            });

            $users = Subscriber::select(['name', 'gender'])->whereIn('id', $uniqueUsers->flatten())->get();
            $users = collect($users);

            $filteredMale = $users->filter(function ($user) {
                return $user->gender == "male";
            });
            $filteredFemale = $users->filter(function ($user) {
                return $user->gender == "female";
            });
            $filteredNull = $users->filter(function ($user) {
                return $user->gender == "" || $user->gender == null;
            });

            $res = [
                'labels' => ['Feminino', 'Masculino', "Não informado"],
                'feminino' => $filteredFemale->count(),
                'masculino' => $filteredMale->count(),
                'naoInformado' => $filteredNull->count(),
            ];

            return $this->customJsonResponse('Dados carregados com sucesso', 200, $res);
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400);
        }
    }

    /**
     * Hits by Location (all hits)
     * @param Request $request
     * @return JsonResponse|void
     */
    public function hitsByLocation(Request $request)
    {
        try {
            $data = $this->cacheApi('getLogs', $request->period);
            $countLocations = collect($data->data)->map(function ($item) {
                return $item->userLocation;
            })->countBy();

            $locations = $count = [];
            foreach ($countLocations as $index => $value) {
                $locations[] = trim($index) !== "" ? $index : 'Não informado';
                $count[] = $value;
            }

            return $this->customJsonResponse('Dados carregados com sucesso', 200, ['labels' => $locations, 'data' => $count]);
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400);
        }
    }

    /**
     * Average Access Time on Platform (need Refact)
     * @param Request $request
     * @return JsonResponse|void
     */
    public function avgAccessTime(Request $request)
    {
        try {
            // Return a array with 2 positions the initial and final periods in EN format.
            $period = $this->convertPeriodToEn($request->period);
            $initialDate = $period['initialDate'];
            $finalDate = $period['finalDate'];

            /* Period Average */
            $sql = $this->accessLog->avgAccessTime($initialDate, $finalDate, intval($request->allDate), Auth::user()->platform_id);
            $avgTime = DB::select($sql);
            $avgTimeFinal = date('H:i:s', strtotime($avgTime[0]->avg_time_access));
            $qtdAccess = $avgTime[0]->registers;

            /* All Period Average */
            $sql = $this->accessLog->avgAccessTime($initialDate, $finalDate, 1, Auth::user()->platform_id);
            $AllAvgTime = DB::select($sql);
            $AllAvgTimeFinal = date('H:i:s', strtotime($AllAvgTime[0]->avg_time_access));
            $AllQtdAccess = $AllAvgTime[0]->registers;

            return response()->json([
                'avg_time' => $avgTimeFinal,
                'qtd_access' => $qtdAccess,
                'total_avg_time' => $AllAvgTimeFinal,
                'total_qtd_access' => $AllQtdAccess,
                'percentage' => $this->rule3($avgTimeFinal, $AllAvgTimeFinal),
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => true, 'response' => $e->getMessage()]);
        }
    }

    /**
     * Rule of Three for calc average time on Platform
     * @param mixed $total
     * @param mixed $val2
     * @return string
     */
    public function rule3($total, $val2)
    {
        $totalParsed = date_parse($total);
        $totalSeconds = $totalParsed['hour'] * 3600 + $totalParsed['minute'] * 60 + $totalParsed['second'];

        $val2Parsed = date_parse($val2);
        $val2Seconds = $val2Parsed['hour'] * 3600 + $val2Parsed['minute'] * 60 + $val2Parsed['second'];

        $res = $totalSeconds * 100 / $val2Seconds;
        return round($res) . '%';
    }
}
