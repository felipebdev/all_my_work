<?php

namespace App\Http\Controllers\Api;

use App\Data\Intl;
use App\Helpers\CollectionHelper;
use App\Http\Traits\CustomResponseTrait;
use App\Services\Subscriber\SubscriberProductService;
use App\Subscriber;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class SubscriberProductController extends Controller
{
    use CustomResponseTrait;

    private Subscriber $subscriber;
    private SubscriberProductService $subscriberProductService;

    public function __construct(Subscriber $subscriber, SubscriberProductService $subscriberProductService)
    {
        $this->subscriber = $subscriber;
        $this->subscriberProductService = $subscriberProductService;
        Intl::ptBR();
    }

    /**
     * Display the specified subscriber.
     *
     * @param  int  $id
     * @param Request $request
     * @return JsonResponse
     */
    public function show($id, Request $request): JsonResponse
    {
        $offset = $request->offset ?? 25;

        try
        {
            $subscriber_product = $this->subscriberProductService->getProducts($id, $request->only('search', 'status'));

            $data['subscriber'] = CollectionHelper::paginate($subscriber_product, $offset);
            return $this->customJsonResponse(
                'Dados carregados com sucesso.',
                200,
                $data
            );
        }
        catch (\Exception $exception)
        {
            return $this->customJsonResponse($exception->getMessage(), 400, []);
        }
    }

}
