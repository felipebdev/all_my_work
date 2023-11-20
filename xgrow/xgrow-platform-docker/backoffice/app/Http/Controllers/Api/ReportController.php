<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\CustomResponseTrait;
use App\Services\Report\ReportTransactionService;
use App\Services\ReportAPI\ReportAPIService;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{

    use CustomResponseTrait;

    private ReportAPIService $reportAPIService;

    /**
     * @var ReportTransactionService
     */
    private ReportTransactionService $reportTransactionService;

    /**
     * @param ReportTransactionService $reportTransactionService
     * @param ReportAPIService $reportAPIService
     */
    public function __construct(ReportTransactionService $reportTransactionService,
                                ReportAPIService         $reportAPIService)
    {
        $this->reportTransactionService = $reportTransactionService;
        $this->reportAPIService = $reportAPIService;
    }

    /**
     * Get transactions
     * @param Request $request
     * @return JsonResponse
     * @deprecated :: Changed by report API (transactionReport)
     * TODO :: Fábio se não usar em outro local, remover esta funcionalidade
     */
    public function transactions(Request $request): JsonResponse
    {
        try {

            $offset = $request->input('offset') ?? 25;

            $transactions = $this->reportTransactionService->getTransactions($request)->paginate($offset);

            return $this->customJsonResponse(
                'Dados carregados com sucesso.',
                200,
                [
                    'transactions' => $transactions
                ]
            );

        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }


    /**
     * Full Transaction report connection
     * @param Request $request
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function transactionsReport(Request $request): JsonResponse
    {
        try {
            $offset = $request->input('offset') ?? 25;
            $page = $request->input('page') ?? 1;

            $query = ['page' => $page, 'offset' => $offset];

            $request->input('subscriber_name') ? $query['subscriber_name'] = $request->input('subscriber_name') : null;
            $request->input('subscriber_email') ? $query['subscriber_email'] = $request->input('subscriber_email') : null;
            $request->input('subscriber_document_number') ? $query['subscriber_document_number'] = $request->input('subscriber_document_number') : null;
            $request->input('subscriber_last_access') ? $query['subscriber_last_access'] = $request->input('subscriber_last_access') : null;
            $request->input('subscriber_credit_cards_last_four_digits') ? $query['subscriber_credit_cards_last_four_digits'] = $request->input('subscriber_credit_cards_last_four_digits') : null;
            $request->input('client_cpf') ? $query['client_cpf'] = $request->input('client_cpf') : null;
            $request->input('client_name') ? $query['client_name'] = $request->input('client_name') : null;
            $request->input('client_cnpj') ? $query['client_cnpj'] = $request->input('client_cnpj') : null;
            $request->input('client_ids') ? $query['client_ids'] = $request->input('client_ids') : null;
            $request->input('client_platform') ? $query['client_platform'] = $request->input('client_platform') : null;
            $request->input('client_platform_ids') ? $query['client_platform_ids'] = $request->input('client_platform_ids') : null;
            $request->input('client_product_ids') ? $query['client_product_ids'] = $request->input('client_product_ids') : null;
            $request->input('client_plan_ids') ? $query['client_plan_ids'] = $request->input('client_plan_ids') : null;
            $request->input('payment_status') ? $query['payment_status'] = $request->input('payment_status') : null;
            $request->input('payment_date') ? $query['payment_date'] = $request->input('payment_date') : null;
            $request->input('payment_value') ? $query['payment_value'] = $request->input('payment_value') : null;

            $req = $this->reportAPIService->transactionsReport($query);
            $data = ['transactions' => $req];

            return $this->customJsonResponse('Dados carregados com sucesso.', 200, $data);
        } catch (\Exception $exception) {
            return $this->customJsonResponse($exception->getMessage(), 400, []);
        }
    }
}
