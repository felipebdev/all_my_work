<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\Finances\InvalidPaymentException;
use App\Exceptions\Finances\InvalidDataException;
use App\Exceptions\Finances\InvalidTwoFactorCodeException;
use App\Exceptions\Finances\RefundFailedException;
use App\Exceptions\Finances\TransactionNotFoundException;
use App\Facades\JwtStudentsFacade;
use App\Facades\JwtWebFacade;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateRefundByStudentsRequest;
use App\Http\Requests\SendTwoFactorCodeRequest;
use App\Http\Requests\CheckTwoFactorCodeRequest;
use App\Http\Requests\CreateRefundRequest;
use App\Http\Traits\CustomResponseTrait;
use App\Services\Finances\Refund\Objects\BankRefund;
use App\Services\Finances\Refund\Objects\PaymentRefund;
use App\Services\Finances\Refund\Objects\RefundOptions;
use App\Services\Finances\Refund\Objects\UserInfo;
use App\Services\Finances\Refund\RefundService;
use Illuminate\Http\Response;

class RefundController extends Controller
{

    use CustomResponseTrait;

    private RefundService $refundService;

    public function __construct(RefundService $refundService)
    {
        $this->refundService = $refundService;
    }

    /**
     * Creates a refund
     *
     * @param  \App\Http\Requests\CreateRefundRequest  $request
     * @return \Illuminate\Http\JsonResponse|void
     * @throws \Exception
     */
    public function store(CreateRefundRequest $request)
    {
        $payload = JwtWebFacade::getPayload();
        $userInfo = new UserInfo($payload->platform_id, $payload->user_id);
        $paymentDataRefund = PaymentRefund::fromArray($request->validated());
        $bankData = BankRefund::fromArray($request->validated());
        $refundOptions = RefundOptions::fromArray($request->validated());

        try {
            $data = $this->refundService->refundUser($userInfo, $paymentDataRefund, $refundOptions, $bankData);
            return response()->json($data);
        } catch (InvalidPaymentException $e) {
            $this->customAbort('Estorno não autorizado', Response::HTTP_UNAUTHORIZED);
        } catch (TransactionNotFoundException $e) {
            $this->customAbort('Transação não encontrada', Response::HTTP_NOT_FOUND);
        } catch (RefundFailedException $e) {
            $this->customAbort('Falha ao estornar: '.$e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function storeByStudents(CreateRefundByStudentsRequest $request)
    {
        $payload = JwtStudentsFacade::getPayload();
        $subscribers = $payload->subscribers_ids;
        $token = $request->code;
        $paymentDataRefund = PaymentRefund::fromArray($request->validated());
        $bankData = BankRefund::fromArray($request->validated());
        $refundOptions = RefundOptions::fromArray($request->validated());

        try {
            $data = $this->refundService->refundStudent($subscribers, $paymentDataRefund, $refundOptions, $token, $bankData);
            return response()->json($data);
        } catch (InvalidPaymentException $e) {
            $this->customAbort('Estorno não autorizado', Response::HTTP_UNAUTHORIZED);
        } catch (TransactionNotFoundException $e) {
            $this->customAbort('Transação não encontrada', Response::HTTP_NOT_FOUND);
        } catch (RefundFailedException $e) {
            $this->customAbort('Falha ao estornar: '.$e->getMessage(), Response::HTTP_BAD_REQUEST);
        }

    }

    public function sendTwoFactorCode(SendTwoFactorCodeRequest $request)
    {

        try {
            $data = $this->refundService->sendTwoFactorCode($request);
            return response()->json($data, 200);
        } catch (InvalidDataException $e) {
            $this->customAbort('Transação não encontrada: '.$e->getMessage(), Response::HTTP_BAD_REQUEST);
        }

    }

    public function checkTwoFactorCode(CheckTwoFactorCodeRequest $request)
    {

        try {
            $data = $this->refundService->checkTwoFactorCode($request);
            return response()->json($data, 200);
        } catch (InvalidDataException $e) {
            $this->customAbort('Transação não encontrada: '.$e->getMessage(), Response::HTTP_BAD_REQUEST);
        } catch (InvalidTwoFactorCodeException $e) {
            $this->customAbort('Erro: '.$e->getMessage(), Response::HTTP_BAD_REQUEST);
        } catch (InvalidPaymentException $e) {
            $this->customAbort('Erro: '.$e->getMessage(), Response::HTTP_BAD_REQUEST);
        }

    }

}
