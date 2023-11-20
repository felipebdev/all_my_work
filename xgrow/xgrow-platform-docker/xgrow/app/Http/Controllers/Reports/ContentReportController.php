<?php

namespace App\Http\Controllers\Reports;

use App\AccessLog;
use App\Content;
use App\ContentLog;
use App\ContentSubscriber;
use App\ContentView;
use App\Http\Controllers\Controller;
use App\Course;
use App\Http\Traits\CustomResponseTrait;
use App\Services\Reports\ContentReportService;
use App\Subscriber;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ContentReportController extends Controller
{
    private $accessLog;
    private $contentLog;
    private $content;
    private $subscriber;
    private $course;
    private $contentSubscriber;
    private $contentView;

    use CustomResponseTrait;

    public function __construct(
        AccessLog $accessLog,
        ContentLog $contentLog,
        Content $content,
        Subscriber $subscriber,
        Course $course,
        ContentSubscriber $contentSubscriber,
        ContentView $contentView
    ) {
        $this->accessLog = $accessLog;
        $this->contentLog = $contentLog;
        $this->content = $content;
        $this->subscriber = $subscriber;
        $this->course = $course;
        $this->contentSubscriber = $contentSubscriber;
        $this->contentView = $contentView;
    }

    private $redisTime = 3600 * 1;

    /**
     * Init first API Cache
     * @param $period
     */
    public function cacheApi($type, $period, $order = 'ASC')
    {
        $platformId = Auth::user()->platform_id;
        $period = $this->convertPeriodToEn($period);
        $initialDate = $period['initialDate'];
        $finalDate = $period['finalDate'];

        $data = '';

        if ($type == 'countComments') {
            $data = Cache::store('redis')->remember(
                "reports:content:countComments:{$platformId}:{$initialDate}-{$finalDate}",
                $this->redisTime,
                function () use ($initialDate, $finalDate) {
                    return (new ContentReportService())->countComments($initialDate, $finalDate, 'createComment');
                }
            );
        }

        if ($type == 'mostAccessed') {
            $data = Cache::store('redis')->remember(
                "reports:content:mostAccessed:{$platformId}:{$initialDate}-{$finalDate}",
                $this->redisTime,
                function () use ($initialDate, $finalDate) {
                    return (new ContentReportService())->mostAccessedContent($initialDate, $finalDate, 'courseView');
                }
            );
        }

        if ($type == 'mostLiked') {
            $data = Cache::store('redis')->remember(
                "reports:content:mostLiked:{$platformId}:{$initialDate}-{$finalDate}",
                $this->redisTime,
                function () use ($initialDate, $finalDate) {
                    return (new ContentReportService())->mostLikedContent($initialDate, $finalDate, 'createLike');
                }
            );
        }

        if ($type == 'mostAccessedSection') {
            $data = Cache::store('redis')->remember(
                "reports:content:mostAccessedSection:{$platformId}:{$initialDate}-{$finalDate}",
                $this->redisTime,
                function () use ($initialDate, $finalDate) {
                    return (new ContentReportService())->mostAccessedSection($initialDate, $finalDate, 'sectionView');
                }
            );
        }

        if ($type == 'contentPopular') {
            $data = Cache::store('redis')->remember(
                "reports:content:contentPopular:{$platformId}:{$initialDate}-{$finalDate}",
                $this->redisTime,
                function () use ($initialDate, $finalDate) {
                    return (new ContentReportService())->contentMostPopularByAuthor($initialDate, $finalDate, 'contentView');
                }
            );
        }

        return $data;
    }

    /**
     * Initial Page of contents report
     * @return View|Factory
     * @throws BindingResolutionException
     */
    public function index()
    {
        $initialDate = Carbon::now()->subMonths(1)->format('d/m/Y');
        $finalDate = Carbon::now()->format('d/m/Y');;
        $period = $initialDate . ' - ' . $finalDate;
        $search = ['period' => $period];
        return view('reports.content.content', compact('search'));
    }

    /**
     * Get list of top 20 more and less commented contents
     * @param Request $request
     * @return JsonResponse|void
     */
    public function countCommentedContent(Request $request)
    {
        try {
            $data = $this->cacheApi('countComments', $request->period);

            $res = [];
            foreach ($data as $item) {
                array_push($res, [
                    "filename" => $item->filename ?? '/xgrow-vendor/assets/img/icon-file.png',
                    "title" => $item->title,
                    "count_comments" => $item->count_comments
                ]);
            }

            return $this->customJsonResponse('Dados carregados com sucesso', 200, ['data' => $res]);
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400);
        }
    }

    /**
     * Get top 20 most accessed contents in desc order
     * @param Request $request
     * @return JsonResponse|void
     */
    public function mostAccessedContent(Request $request)
    {
        try {
            $data = $this->cacheApi('mostAccessed', $request->period);
            $res = [];
            foreach ($data as $item) {
                $item = (object)$item;
                array_push($res, [
                    "filename" => $item->filename ?? '/xgrow-vendor/assets/img/icon-file.png',
                    "title" => $item->title,
                    "amount" => $item->amount
                ]);
            }

            return $this->customJsonResponse('Dados carregados com sucesso', 200, ['data' => $res]);
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400);
        }
    }

    /**
     * Get top 20 most liked in desc order
     * @param Request $request
     * @return JsonResponse|void
     */
    public function mostLikedContent(Request $request)
    {
        try {
            $data = $this->cacheApi('mostLiked', $request->period);
            $res = [];
            foreach ($data as $item) {
                $item = (object)$item;
                array_push($res, [
                    "filename" => $item->filename ?? '/xgrow-vendor/assets/img/icon-file.png',
                    "title" => $item->title,
                    "likes" => $item->likes
                ]);
            }

            return $this->customJsonResponse('Dados carregados com sucesso', 200, ['data' => $data]);
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400);
        }
    }

    /**
     * Get most accessed section and displayed on graph
     * @param Request $request
     * @return JsonResponse|void
     */
    public function mostAccessedSection(Request $request)
    {
        try {
            $res = $this->cacheApi('mostAccessedSection', $request->period);
            $labels = $data = [];
            foreach ($res as $item) {
                $item = (object)$item;
                $labels[] = $item->name;
                $data[] = $item->amount;
            }

            return $this->customJsonResponse('Dados carregados com sucesso', 200, ['labels' => $labels, 'data' => $data]);
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400);
        }
    }

    /**
     * Get amount of content view by authors
     * @param Request $request
     * @return JsonResponse|void
     */
    public function contentViewsByAuthor(Request $request)
    {
        try {
            $res = $this->cacheApi('contentPopular', $request->period);
            $labels = $data = [];
            foreach ($res as $key => $value) {
                $labels[] = ['name' => 'Autor: ' . $key];
                $data[] = ['name' => 'Autor: ' . $key, 'value' => $value];
            }
            return $this->customJsonResponse('Dados carregados com sucesso', 200, ['labels' => $labels, 'data' => $data]);
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

    // TODO Verify if necessary
    public function contentMostAccessedByAuthor(Request $request)
    {
        try {
            // Return a array with 2 positions the initial and final periods in EN format.
            $period = $this->convertPeriodToEn($request->period);
            $sql = $this->content->contentMostAccessedByAuthor($period['initialDate'], $period['finalDate'], intval($request->allDate), Auth::user()->platform_id);

            $contentMostAccessedByAuthor = DB::select($sql);
            $labels = $data = [];
            foreach ($contentMostAccessedByAuthor as $item) {
                $labels[] = ['name' => 'Autor: ' . $item->name_author . ' - ' . $item->title];
                $data[] = ['name' => 'Autor: ' . $item->name_author . ' - ' . $item->title, 'value' => $item->amount];
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
