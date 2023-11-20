<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\Finances\ActionFailedException;
use App\Exceptions\Finances\BankInformationAlreadyExistsException;
use App\Exceptions\Finances\InvalidBankAccountException;
use App\Exceptions\Finances\RecipientNotExistsException;
use App\Exceptions\Finances\RecipientNotFound;
use App\Facades\JwtWebFacade;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UpdateBankAccountRequest;
use App\Http\Traits\CustomResponseTrait;
use App\Services\Finances\BankAccount\BankAccountService;
use App\Services\Finances\BankAccount\Objects\BankAccount;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class BankController extends Controller
{

    use CustomResponseTrait;

    private BankAccountService $bankAccountService;

    public function __construct(BankAccountService $bankAccountService)
    {
        $this->bankAccountService = $bankAccountService;
    }

    public function get(Request $request)
    {
        $payload = JwtWebFacade::getPayload();

        try {
            $balance = $this->bankAccountService->getDefaultUserBankAccount(
                $payload->user_id
            );

            return response()->json($balance);
        } catch (RecipientNotFound $e) {
            $this->customAbort('Recebedor não encontrado', Response::HTTP_NOT_FOUND);
        } catch (RecipientNotExistsException $e) {
            $this->customAbort('Recebedor inexistente', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(UpdateBankAccountRequest $request)
    {
        $payload = JwtWebFacade::getPayload();

        $bankAccount = BankAccount::fromArray($request->validated());

        try {
            $bankAccount = $this->bankAccountService->createRecipientForBankInformation(
                $payload->user_id,
                $bankAccount
            );

            return response()->json($bankAccount);
        } catch (BankInformationAlreadyExistsException $e) {
            $this->customAbort('Usuario já possui dados bancários cadastrados', Response::HTTP_CONFLICT);
        } catch (RecipientNotFound $e) {
            $this->customAbort('Recebedor não encontrado', Response::HTTP_NOT_FOUND);
        } catch (RecipientNotExistsException $e) {
            $this->customAbort('Recebedor inexistente', Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (InvalidBankAccountException $e) {
            Log::debug('invalid-bank-account',
                [
                    'data' => $request->all(),
                    'reason' => $e->getMessage(),
                    'code' => $e->getCode(),
                    'line' => $e->getLine(),
                    'file' => $e->getFile()
                ]
            );

            $this->customAbort('Banco inválido: '.$e->getMessage(), Response::HTTP_BAD_REQUEST);
        }  catch (ActionFailedException $e) {
            $this->customAbort('Falha desconhecida: '.$e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(UpdateBankAccountRequest $request)
    {
        $payload = JwtWebFacade::getPayload();

        $bankAccount = BankAccount::fromArray($request->validated());

        try {
            $bankAccount = $this->bankAccountService->changeAllUserBankAccounts(
                $payload->user_id,
                $bankAccount
            );

            return response()->json($bankAccount);
        } catch (RecipientNotFound $e) {
            $this->customAbort('Recebedor não encontrado', Response::HTTP_NOT_FOUND);
        } catch (RecipientNotExistsException $e) {
            $this->customAbort('Recebedor inexistente', Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (InvalidBankAccountException $e) {
            Log::debug('invalid-bank-account',
                [
                    'data' => $request->all(),
                    'reason' => $e->getMessage(),
                    'code' => $e->getCode(),
                    'line' => $e->getLine(),
                    'file' => $e->getFile()
                ]
            );

            $this->customAbort('Banco inválido: '.$e->getMessage(), Response::HTTP_BAD_REQUEST);
        }  catch (ActionFailedException $e) {
            $this->customAbort('Falha ao realizar ação: '.$e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
