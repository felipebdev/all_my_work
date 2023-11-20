<?php

namespace App\Http\Controllers\Subscriber;

use App\ContentSubscriber;
use App\CourseSubscriber;
use App\Helpers\CollectionHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Subscriber\StoreSubscriberRequest;
use App\Http\Traits\CustomResponseTrait;
use App\PaymentCards;
use App\Plan;
use App\Safe;
use App\Services\Contracts\SubscriberReportServiceInterface;
use App\Services\EmailService;
use App\Services\LA\CacheClearService;
use App\Services\Objects\SubscriberReportFilter;
use App\Subscriber;
use App\Subscription;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class SubscriberListController extends Controller
{
    use CustomResponseTrait;

    const MYSQ_CODE_INTEGRITY_CONSTANT_VIOLATION = 23000;

    private SubscriberReportServiceInterface $subscriberReportService;
    private EmailService $emailService;
    private CacheClearService $cacheClearService;

    public function __construct(
        SubscriberReportServiceInterface $subscriberReportService,
        EmailService $emailService,
        CacheClearService $cacheClearService
    ) {
        $this->subscriberReportService = $subscriberReportService;
        $this->emailService = $emailService;
        $this->cacheClearService = $cacheClearService;
    }

    public function searchSubscriber(Request $request)
    {
        try {
            $offset = $request->input('offset') ?? 25;
            $createdPeriodFilter = $request->input('createdPeriodFilter') ?? ['', ''];
            $lastAccessPeriodFilter = $request->input('lastAccessPeriodFilter') ?? ['', ''];
            $neverAccessedFilter = $request->input('neverAccessedFilter') === 'true';
            $emailWrongFilter = $request->input('emailWrongFilter') === 'true';

            $filters = new SubscriberReportFilter(
                $request->input('searchTermFilter'),
                $request->input('plansFilter'),
                $request->input('statusFilter'),
                parseBrDate($createdPeriodFilter[0]),
                parseBrDate($createdPeriodFilter[1]),
                parseBrDate($lastAccessPeriodFilter[0]),
                parseBrDate($lastAccessPeriodFilter[1]),
                $neverAccessedFilter,
                $emailWrongFilter,
            );

            $subscribers = $this->subscriberReportService
                ->getSubscriberReport(Auth::user()->platform_id, $filters)
                ->orderBy('subscribers.created_at', 'DESC')
                ->get();

            $collection = CollectionHelper::paginate($subscribers, $offset);

            return $this->customJsonResponse('Dados carregados com sucesso.', Response::HTTP_OK, [
                'subscribers' => $collection,
            ]);
        } catch (Exception $e) {
            return $this->customJsonResponse('Falha ao obter dados', Response::HTTP_BAD_REQUEST, [
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function storeSubscriber(StoreSubscriberRequest $request)
    {
        $plan = Plan::query()->where('id', $request->plan_id)->first();
        if (!$plan) {
            return $this->customJsonResponse('Plano não encontrado', Response::HTTP_NOT_FOUND);
        }

        if ($plan->platform_id != Auth::user()->platform_id) {
            return $this->customJsonResponse('Plano não encontrado na plataforma', Response::HTTP_NOT_FOUND);
        }

        $subscriber = Subscriber::query()
            ->where('platform_id', $plan->platform_id)
            ->where('email', $request->email)
            ->first();

        $subscriberStatus = $subscriber->status ?? null;
        if ($subscriber && $subscriberStatus != Subscriber::STATUS_LEAD) {
            return $this->customJsonResponse('Email já cadastrado', Response::HTTP_CONFLICT);
        }

        if ($subscriberStatus == Subscriber::STATUS_LEAD) {
            $subscriber->update([
                'status' => Subscriber::STATUS_ACTIVE,
            ]);
        } else {
            $subscriber = $plan->subscribers()->create([
                'status' => Subscriber::STATUS_ACTIVE,
                'platform_id' => $plan->platform_id,
                'email' => $request->email,
                'name' => $request->full_name,
                'source_register' => Subscriber::SOURCE_PLATFORM,
            ]);
        }

        $subscription = $subscriber->subscriptions()->create([
            'platform_id' => $plan->platform_id,
            'plan_id' => $plan->id,
            'status' => Subscription::STATUS_ACTIVE,
        ]);

        $sent = $this->emailService->sendMailNewRegisterSubscriber($subscriber);
        if ($sent) {
            $message = 'Cadastrado com sucesso, dados de acesso serão enviados por email';
            return $this->customJsonResponse($message, Response::HTTP_ACCEPTED);
        }

        return $this->customJsonResponse('Cadastrado com sucesso', Response::HTTP_OK);
    }

    public function resendData($id)
    {
        $subscriber = Subscriber::find($id);

        if (!$subscriber) {
            return $this->customJsonResponse('Assinante não foi encontrado!', Response::HTTP_NOT_FOUND);
        }

        $subscriptions = $subscriber->subscriptions->first();

        if (!$subscriptions) {
            $message = 'Para enviar os dados de acesso é necessário que o aluno possua pelo menos um produto cadastrado.';
            return $this->customJsonResponse($message, Response::HTTP_NOT_FOUND);
        }

        $sent = $this->emailService->sendMailNewRegisterSubscriber($subscriber);

        if (!$sent) {
            $message = 'Plano desse assinante não está habilitado para envio de e-mail!';
            return $this->customJsonResponse($message, Response::HTTP_NOT_FOUND);
        }

        return $this->customJsonResponse('Dados enviados com sucesso');
    }

    public function destroy(Request $request, $subscriber_id)
    {
        try {
            $this->deleteSubscriberById(Auth::user()->platform_id, $subscriber_id);

            return response()->json(['response' => 'success']);
        } catch (QueryException $e) {
            if ($e->getCode() == self::MYSQ_CODE_INTEGRITY_CONSTANT_VIOLATION) {
                return $this->customJsonResponse('possui comentários e/ou pagamentos registrados na plataforma', 500, [
                    'response' => 'fail',
                ]);
            }

            return $this->customJsonResponse($e->getMessage(), 500, [
                'response' => 'fail',
            ]);
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 500, [
                'response' => 'fail',
            ]);
        }
    }

    /**
     * @param  string  $platformId
     * @param  string  $subscriberId
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @todo Move it to a repository
     */
    private function deleteSubscriberById(string $platformId, string $subscriberId): void
    {
        $subscriber = Subscriber::find($subscriberId);

        Safe::query()->where('subscriber_id', $subscriber->id)->delete();
        PaymentCards::query()->where('subscriber_id', $subscriber->id)->delete();
        Subscription::query()->where('subscriber_id', $subscriber->id)->delete();
        CourseSubscriber::query()->where('subscriber_id', $subscriber->id)->delete();
        ContentSubscriber::query()->where('subscriber_id', $subscriber->id)->delete();

        $this->cacheClearService->clearSubscriberCache($platformId, $subscriber->email, $subscriber->id);

        $subscriber->delete();
    }


}
