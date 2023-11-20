<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\Finances\InsuficientFundsException;
use App\Exceptions\Finances\InvalidRecipientException;
use App\Exceptions\Finances\TransferCanceledException;
use App\Exceptions\Finances\TransferNotFoundException;
use App\Facades\JwtWebFacade;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateTransferRequest;
use App\Http\Requests\ListTransferRequest;
use App\Http\Traits\CustomResponseTrait;
use App\Services\Finances\Objects\Coin;
use App\Services\Finances\Transfer\Objects\TransferFilter;
use App\Services\Finances\Transfer\TransferService;
use Illuminate\Http\Response;

class TransferController extends Controller
{

    use CustomResponseTrait;

    private TransferService $transferService;

    public function __construct(TransferService $transferService)
    {
        $this->transferService = $transferService;
    }

    /**
     * List transfers with optional filter
     *
     * @param  \App\Http\Requests\ListTransferRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(ListTransferRequest $request)
    {
        $payload = JwtWebFacade::getPayload();
        $filter = TransferFilter::fromArray($request->validated());

        $transfers = $this->transferService->listUserTransfers(
            $payload->platform_id,
            $payload->user_id,
            $payload->acting_as ?? 'client',
            $filter
        );

        return response()->json($transfers);
    }

    /**
     * Creates a new transfer
     *
     * @param  \App\Http\Requests\CreateTransferRequest  $request
     * @return \App\Services\Finances\Transfer\Objects\TransferResponse|\Illuminate\Http\JsonResponse
     */
    public function store(CreateTransferRequest $request)
    {
        $payload = JwtWebFacade::getPayload();
        $userId = $payload->user_id; // user that requested the transfer

        if ($this->transferService->hasReachedLimit($userId)) {
            $message = 'Muitas tentativas malsucedidas, entre em contato com o suporte';
            $this->customAbort($message, Response::HTTP_TOO_MANY_REQUESTS);
        }

        $coin = Coin::fromInt($request->amount);

        try {
            $data = $this->transferService->userTransfer(
                $payload->platform_id,
                $userId,
                $payload->acting_as ?? 'client',
                $coin,
                $request->message,
                $request->metadata
            );

            return response()->json($data);
        } catch (InsuficientFundsException $e) {
            $this->customAbort('Saldo insuficiente', Response::HTTP_CONFLICT);
        } catch (InvalidRecipientException $e) {
            $this->customAbort('Recebedor não autorizado', Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Get info about single transfer
     *
     * @param $transfer
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($transfer)
    {
        try {
            $payload = JwtWebFacade::getPayload();

            $data = $this->transferService->getSingleUserTransfer($payload->platform_id, $payload->user_id, $transfer);

            return response()->json($data);
        } catch (TransferNotFoundException | InvalidRecipientException $e) {
            $this->customAbort('Tranferência não encontrada para o usuário', Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Cancel a single transfer
     *
     * @param $transfer
     * @return \Illuminate\Http\JsonResponse|void
     * @throws \Exception
     */
    public function destroy($transfer)
    {
        try {
            $payload = JwtWebFacade::getPayload();

            $data = $this->transferService->cancelUserTransfer($payload->platform_id, $payload->user_id, $transfer);

            return response()->json($data);
        } catch (TransferNotFoundException | InvalidRecipientException $e) {
            $this->customAbort('Tranferência não encontrada para o usuário', Response::HTTP_NOT_FOUND);
        } catch (TransferCanceledException $e) {
            $this->customAbort('Tranferência anteriormente cancelada', Response::HTTP_GONE);
        }
    }
}
