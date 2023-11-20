<?php

namespace App\Http\Controllers\Reports;

use App\Content;
use App\Helpers\CollectionHelper;
use App\Http\Controllers\Controller;
use App\Http\Traits\CustomResponseTrait;
use App\Services\LAService;
use App\Services\Reports\ResearchReportService;
use App\Subscriber;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ResearchReportController extends Controller
{

    use CustomResponseTrait;

    /**
     * Return the LA Service connection
     * @return LAService
     */
    private function laService(): LAService
    {
        $platform_id = Auth::user()->platform_id;
        $user_id = Auth::user()->id;
        return new LAService($platform_id, $user_id);
    }

    /**
     * Initial Research
     * @return View|Factory
     * @throws BindingResolutionException
     */
    public function index()
    {
        return view('reports.research.index');
    }

    /**
     * Get data access from LA API
     * @param Request $request
     * @return never
     */
    public function getResearchAPI(Request $request)
    {
        try {
            $offset = $request->input('offset') ?? 25;

            $period = $this->convertPeriodToEn($request->input('period'));

            $response = (new ResearchReportService)->getLogs($period['initialDate'], $period['finalDate'], 'contentView');

            $data = collect($response)->map(function ($log) {
                return ['userId' => $log->userId, 'actionId' => $log->actionId, 'userIp' => $log->userIp, 'createdAt' => $log->createdAt];
            });

            // Subscribers
            $subCollection = $data->map(function ($log) {
                return ['userId' => $log['userId']];
            })->unique('userId');

            $subscribers = Subscriber::select('id', 'name')
                ->whereIn('id', $subCollection->flatten())
                ->get();

            // Contents
            $contentCollection = $data->map(function ($log) {
                return ['actionId' => $log['actionId']];
            })->unique('actionId');

            $contents = Content::select('id', 'title')
                ->whereIn('id', $contentCollection->flatten())
                ->get();

            $subscribers = $subscribers->toArray();
            $contents = $contents->toArray();

            // Merge All Collections
            $data = $data->map(function ($log) use ($subscribers, $contents) {
                return [
                    'userId' => collect($subscribers)->firstWhere('id', $log['userId']) ?? ['name' => '-'],
                    'actionId' => collect($contents)->firstWhere('id', $log['actionId']) ?? ['title' => '-'],
                    'userIp' => $log['userIp'],
                    'createdAt' => $log['createdAt'],
                ];
            });

            // Need create specific filters for search
            $filter = $request->filter ?? null;
            $filterContent = $request->content ?? null;

            if ($filter) {
                $data = $data->filter(function ($term) use ($filter) {
                    return str_contains(strtolower($term['userId']['name']), strtolower($filter));
                });
            }

            if ($filterContent) {
                $data = $data->filter(function ($term) use ($filterContent) {
                    if (in_array($term['actionId']['title'], $filterContent)) return $term;
                });
            }

            $data = CollectionHelper::paginate($data, $offset);
            $contents = collect($contents)->pluck('title');

            //contentView - sectionView

            return $this->customJsonResponse('', 200, [
                'data' => $data,
                'contents' => $contents
            ]);
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
}
