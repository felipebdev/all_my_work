<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\CustomResponseTrait;
use App\Services\Report\TransactionsService;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class FinancialController extends Controller
{
    use CustomResponseTrait;

    private TransactionsService $transactionService;

    public function __construct(TransactionsService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    /**
     * Get all Transactions
     * @param Request $request
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function getTransactions(Request $request): JsonResponse
    {
        try {
            $period = $request->input('period');
            $page = $request->input('page') ?? 1;
            $searchTerm = $request->input('searchTerm') ?? '';
            $offset = $request->input('offset') ?? 25;

            $searchArray = ['period' => $period, 'page' => $page, 'searchTerm' => $searchTerm, 'offset' => $offset];
            if ($request->has('paymentType')) $searchArray['paymentType'] = $request->input('paymentType');
            if ($request->has('statusPayment')) $searchArray['statusPayment'] = $request->input('statusPayment');
            if ($request->has('paymentMethod')) $searchArray['paymentMethod'] = $request->input('paymentMethod');
            if ($request->has('productsId')) $searchArray['productsId'] = $request->input('productsId');
            if ($request->has('plansId')) $searchArray['plansId'] = $request->input('plansId');
            if ($request->has('onlyWithCoupon')) $searchArray['onlyWithCoupon'] = $request->input('onlyWithCoupon');
            if ($request->has('installmentPaid')) $searchArray['installmentPaid'] = $request->input('installmentPaid');
            if ($request->has('order')) $searchArray['order'] = $request->input('order');
            $data = $this->transactionService->getTransactions($searchArray);

            return $this->customJsonResponse('Dados carregados com sucesso.', 200, [$data]);
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }

    /**
     * Get details from a specific transaction
     * @param Request $request
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function getTransactionsDetails($paymentId): JsonResponse
    {
        try {
            $data = $this->transactionService->getTransactionsDetails($paymentId);

            return $this->customJsonResponse('Dados carregados com sucesso.', 200, [$data]);
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }

    /**
     * Get all No Limit Transactions
     * @param Request $request
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function getNoLimitTransactions(Request $request): JsonResponse
    {
        try {
            $period = $request->input('period');
            $page = $request->input('page') ?? 1;
            $searchTerm = $request->input('searchTerm') ?? '';
            $offset = $request->input('offset') ?? 25;

            $searchArray = ['period' => $period, 'page' => $page, 'searchTerm' => $searchTerm, 'offset' => $offset];
            if ($request->has('statusSubscription')) $searchArray['statusSubscription'] = $request->input('statusSubscription');
            if ($request->has('statusPayment')) $searchArray['statusPayment'] = $request->input('statusPayment');
            if ($request->has('typePayment')) $searchArray['typePayment'] = $request->input('typePayment');
            if ($request->has('periodAccession')) $searchArray['periodAccession'] = $request->input('periodAccession');
            if ($request->has('periodCancel')) $searchArray['periodCancel'] = $request->input('periodCancel');
            if ($request->has('productsId')) $searchArray['productsId'] = $request->input('productsId');
            if ($request->has('periodLastPayment')) $searchArray['periodLastPayment'] = $request->input('periodLastPayment');
            if ($request->has('periodBillingDate')) $searchArray['periodBillingDate'] = $request->input('periodBillingDate');

            $data = $this->transactionService->getNoLimitTransactions($searchArray);

            return $this->customJsonResponse('Dados carregados com sucesso.', 200, [$data]);
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }

    /**
     * Get details from a specific no limit transaction
     * @param Request $request
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function getNoLimitTransactionsDetails($subscriberId, $planId, $paymentOrderNumber): JsonResponse
    {
        try {
            $data = $this->transactionService->getNoLimitTransactionsDetails($subscriberId, $planId, $paymentOrderNumber);

            return $this->customJsonResponse('Dados carregados com sucesso.', 200, [$data]);
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }

    /**
     * Get all No Limit Transactions
     * @param Request $request
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function getSubscriptions(Request $request): JsonResponse
    {
        try {
            $period = $request->input('period');
            $page = $request->input('page') ?? 1;
            $searchTerm = $request->input('searchTerm') ?? '';
            $offset = $request->input('offset') ?? 25;

            $searchArray = ['period' => $period, 'page' => $page, 'searchTerm' => $searchTerm, 'offset' => $offset];
            if ($request->has('productsId')) $searchArray['productsId'] = $request->input('productsId');
            if ($request->has('statusSubscription')) $searchArray['statusSubscription'] = $request->input('statusSubscription');
            if ($request->has('typePayment')) $searchArray['typePayment'] = $request->input('typePayment');
            if ($request->has('statusPayment')) $searchArray['statusPayment'] = $request->input('statusPayment');
            if ($request->has('periodAccession')) $searchArray['periodAccession'] = $request->input('periodAccession');
            if ($request->has('periodCancel')) $searchArray['periodCancel'] = $request->input('periodCancel');
            if ($request->has('periodLastPayment')) $searchArray['periodLastPayment'] = $request->input('periodLastPayment');

            $data = $this->transactionService->getSubscriptions($searchArray);

            return $this->customJsonResponse('Dados carregados com sucesso.', 200, [$data]);
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }

    /**
     * Get details from a specific no subscription
     * @param $subscriberId
     * @param $planId
     * @param $paymentOrderNumber
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function getSubscriptionsDetails($subscriberId, $planId, $paymentOrderNumber): JsonResponse
    {
        try {
            $data = $this->transactionService->getSubscriptionsDetails($subscriberId, $planId, $paymentOrderNumber);

            return $this->customJsonResponse('Dados carregados com sucesso.', 200, [$data]);
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }

    /**
     * Get information about manual tries on a given payment.
     * @param Request $request
     * @param $paymentId
     * @return JsonResponse
     * @throws GuzzleException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function retryPaymentTransaction(Request $request, $paymentId): JsonResponse
    {
        try {
            $data = $this->transactionService->retryPaymentTransaction($paymentId);

            return $this->customJsonResponse('Dados carregados com sucesso.', 200, [$data]);
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }
}
