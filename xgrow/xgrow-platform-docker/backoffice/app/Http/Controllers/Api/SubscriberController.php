<?php

namespace App\Http\Controllers\Api;

use App\Data\Intl;
use App\Http\Requests\Subscriber\ChangeStatusRequest;
use App\Http\Requests\Subscriber\SubscriberRequest;
use App\Http\Traits\CustomResponseTrait;
use App\Services\PlatformAPI\PlatformAPIService;
use App\Services\Subscriber\SubscriberService;
use App\Subscriber;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class SubscriberController extends Controller
{
    use CustomResponseTrait;

    private Subscriber $subscriber;
    private SubscriberService $subscriberService;

    public function __construct(Subscriber $subscriber, SubscriberService $subscriberService)
    {
        $this->subscriber = $subscriber;
        $this->subscriberService = $subscriberService;
        Intl::ptBR();
    }

    /**
     * Get Subscribers
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $offset = $request->input('offset') ?? 25;

            $data['subscribers'] = $this->subscriberService->getSubscribers($request->all(), $offset);

            return $this->customJsonResponse(
                'Dados carregados com sucesso.',
                200,
                $data
            );
        } catch (\Exception $exception) {
            return $this->customJsonResponse($exception->getMessage(), 400, []);
        }
    }

    /**
     * Export Subscribers
     * @param Request $request
     * @return JsonResponse
     */
    public function exportSubscriber(Request $request): JsonResponse
    {
        try {
            $this->subscriberService->exportSubscriber($request->only('search', 'period', 'type'));

            return $this->customJsonResponse(
                'Ação realizada com sucesso.',
                200,
                []
            );
        } catch (\Exception $exception) {
            return $this->customJsonResponse($exception->getMessage(), 400, []);
        }
    }

    /**
     * Get Subscribers Summary
     * @param Request $request
     * @return JsonResponse
     */
    public function summary(Request $request): JsonResponse
    {
        try {

            $summary = $this->subscriberService->getSubscriberSummary($request->only('period'));

            return $this->customJsonResponse(
                'Dados carregados com sucesso.',
                200,
                ['summary' => $summary]
            );
        } catch (\Exception $exception) {
            return $this->customJsonResponse($exception->getMessage(), 400, []);
        }
    }

    /**
     * Store the subscriber.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(SubscriberRequest $request)
    {
        try {
            $subscriber = new Subscriber();
            $subscriber->name = $request->input('name');
            $subscriber->platform_id = $request->input('platform_id');
            $subscriber->email = $request->input('email');
            $subscriber->password = Hash::make(time());
            $subscriber->save();

            return $this->customJsonResponse('Dados cadastrado com sucesso.', 201, [
                'subscriber' => $subscriber,
            ]);
        } catch (\Exception $exception) {
            return $this->customJsonResponse($exception->getMessage(), 500, []);
        }
    }

    /**
     * Display the specified subscriber.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show($id)
    {
        try {
            $subscriber = Subscriber::findOrFail($id);
            $data['subscriber'] = $subscriber;
            return $this->customJsonResponse(
                'Dados carregados com sucesso.',
                200,
                $data
            );
        } catch (\Exception $exception) {
            return $this->customJsonResponse($exception->getMessage(), 400, []);
        }
    }

    /**
     * Update the specified subscriber.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(SubscriberRequest $request, $id)
    {
        try {
            $subscriber = $this->subscriber::findOrFail($id);
            $subscriber->name = $request->input('name');
            $subscriber->save();

            return $this->customJsonResponse('Dados cadastrado com sucesso.', 200, [
                'subscriber' => $subscriber,
                'plan' => $plan
            ]);
        } catch (\Exception $exception) {
            return $this->customJsonResponse($exception->getMessage(), 500, []);
        }
    }

    /**
     * Remove the specified subscriber.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        try {
            $this->subscriberService->delete($id);

            return $this->customJsonResponse('Aluno excluido com sucesso.', 204);
        } catch (\Exception $exception) {
            return $this->customJsonResponse($exception->getMessage(), 500, []);
        }
    }

    /**
     * Subscriber Transaction.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function transaction($id)
    {
        try {
            $transaction = $this->transactionSubscriber->findOrFail($id);
            return $this->customJsonResponse(
                'Dados carregados com sucesso.',
                200,
                $transaction
            );
        } catch (\Exception $exception) {
            return $this->customJsonResponse($exception->getMessage(), 400, []);
        }
    }

    /**
     * List subscribers
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function list(Request $request): JsonResponse
    {
        try {

            $offset = $request->input('offset') ?? 25;
            $subscribers = $this->subscriberService->list($request->only('search'), $offset);

            return $this->customJsonResponse(
                'Dados carregados com sucesso.',
                200,
                [
                    'subscribers' => $subscribers,
                ]
            );
        } catch (\Exception $exception) {
            return $this->customJsonResponse($exception->getMessage(), 400, []);
        }
    }

    /**
     * Get subscriber for select input
     * @param Request $request
     * @return JsonResponse
     */
    public function getByName(Request $request): JsonResponse
    {
        try {
            $offset = $request->input('offset') ?? 25;
            $subscribers = $this->subscriberService->getByName($request->only('search'))
                ->paginate($offset);
            return $this->customJsonResponse('Plataformas encontradas', 200, ['subscribers' => $subscribers]);
        } catch (\Exception $exception) {
            return $this->customJsonResponse($exception->getMessage(), 400, []);
        }
    }

    /**
     * Change subscriber status
     *
     *
     * @param ChangeStatusRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function changeStatus(ChangeStatusRequest $request, int $id): JsonResponse
    {
        try {
            $subscriber = $this->subscriberService->changeStatus($id, $request->input('status'));
            $data = ['subscriber' => $subscriber];

            return $this->customJsonResponse('Status atualizado com sucesso.', 200, $data);
        } catch (\Exception $exception) {
            return $this->customJsonResponse($exception->getMessage(), 500, []);
        }
    }

    /**
     * Resend data email for platform
     * @param PlatformAPIService $service
     * @param int $id
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function resendData(PlatformAPIService $service, int $id): JsonResponse
    {
        try {
            $response = $service->resendData($id);

            return $this->customJsonResponse('Dados reenviados com sucesso.', 200, ['response' => $response]);

        } catch (\Exception $exception) {
            Log::error('Resend Subscriber Data to PlatformAPI :: ERROR-01', [
                'method' => 'POST',
                'uri' => "/api/backoffice/subscribers/resend-data",
                'code' => $exception->getCode()
            ]);
            return $this->customJsonResponse($exception->getMessage());
        }
    }

}
