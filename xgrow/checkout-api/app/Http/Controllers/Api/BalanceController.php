<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\Finances\RecipientNotExistsException;
use App\Exceptions\Finances\RecipientNotFound;
use App\Facades\JwtWebFacade;
use App\Http\Controllers\Controller;
use App\Http\Traits\CustomResponseTrait;
use App\Services\Finances\Balance\RecipientBalanceService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BalanceController extends Controller
{

    use CustomResponseTrait;

    private RecipientBalanceService $recipientBalance;

    public function __construct(RecipientBalanceService $recipientBalance)
    {
        $this->recipientBalance = $recipientBalance;
    }

    public function get(Request $request)
    {
        $payload = JwtWebFacade::getPayload();

        try {
            $balance = $this->recipientBalance->getUserBalance(
                $payload->platform_id,
                $payload->user_id,
                $payload->acting_as ?? 'client'
            );

            return response()->json($balance);
        } catch (RecipientNotFound $e) {
            $this->customAbort('Recebedor não encontrado na plataforma', Response::HTTP_NOT_FOUND);
        } catch (RecipientNotExistsException $e) {
            $this->customAbort('Recebedor inexistente', Response::HTTP_NOT_FOUND);
        }
    }

}
