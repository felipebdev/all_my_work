<?php

namespace App\Http\Controllers\Subscriber;

use App\Services\LAService;
use App\Subscriber;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use stdClass;
use App\Helpers\CollectionHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use App\Http\Traits\CustomResponseTrait;
use App\Services\LA\CacheClearService;

/**
 *
 */
class SubscriberBlockedsController extends Controller
{
    use CustomResponseTrait;

    /**
     * @var Subscriber
     */
    private Subscriber $subscriber;
    /**
     * @var CacheClearService
     */
    private CacheClearService $cacheClearService;

    /**
     * @param  Subscriber  $subscriber
     * @param  CacheClearService  $cacheClearService
     */
    public function __construct(Subscriber $subscriber, CacheClearService $cacheClearService)
    {
        $this->subscriber = $subscriber;
        $this->cacheClearService = $cacheClearService;
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function searchBlockedSubscriber(Request $request): JsonResponse
    {
        $nameFilter = $request->input('nameFilter');
        $offset = $request->input('offset') ?? 25;

        try {
            $laApiService = new LAService(
                Auth::user()->platform_id,
                Auth::user()->id
            );

            $blockedSubscribers = (!empty(env('LA_PLATFORM_CONFIGURATION_API'))
                ? $laApiService->listBlockedAccesses()
                : new stdClass()
            );

            if (property_exists($blockedSubscribers, 'data')) {
                $blockedSubscribers->data = array_map(function ($subs) {
                    $result = $this->subscriber->where('id', $subs->userId)->first();

                    $subs->userName = $result->name ?? '-';
                    $subs->userEmail = $result->email ?? '-';

                    return $subs;
                }, $blockedSubscribers->data);
            }

            $data = collect($blockedSubscribers->data ?? []);

            if ($nameFilter) {
                $data = $data->filter(function ($item) use ($nameFilter) {
                    return Str::contains(
                        Str::lower($item->userName),
                        Str::lower($nameFilter)
                    ) || Str::contains(
                        Str::lower($item->userEmail),
                        Str::lower($nameFilter)
                    );
                });
            }

            $blocked = $request->input('blocked');

            if ($blocked !== null) {
                $data = $data->filter(function ($item) use ($blocked) {
                    if ($item->isLocked == $blocked) {

                        return $item;
                    }
                });
            }

            $collection = CollectionHelper::paginate($data, $offset);

            return $this->customJsonResponse(
                'Dados carregados com sucesso.',
                Response::HTTP_OK,
                ['subscribers' => $collection]
            );
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'response' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function updateBlockedSubscriber(Request $request): JsonResponse
    {
        try {
            $laApiService = new LAService(
                Auth::user()->platform_id,
                Auth::user()->id
            );

            $updated = $laApiService->updateBlockedAccess(
                $request->userId,
                $request->action
            );

            $this->cacheClearService->clearSubscriberCache(Auth::user()->platform_id, $request->userId);

            $message = $request->action == 0 ? 'Acesso Liberado' : 'Acesso Bloqueado';

            return $this->customJsonResponse(
                'Dados carregados com sucesso.',
                Response::HTTP_OK,
                ['subscriber' => $message]
            );
        } catch (Exception $e) {
            return response()->json([
                'error' => true,
                'response' => $e->getMessage()
            ], 400);
        }
    }
}
