<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\Finances\ActionFailedException;
use App\Exceptions\Finances\InvalidRecipientException;
use App\Facades\JwtWebFacade;
use App\Http\Controllers\Controller;
use App\Http\Traits\CustomResponseTrait;
use App\Services\Finances\Recipient\RecipientsPlanService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RecipientsPlanController extends Controller
{

    use CustomResponseTrait;

    private RecipientsPlanService $recipientsPlanService;

    public function __construct(RecipientsPlanService $recipientsPlanService)
    {
        $this->recipientsPlanService = $recipientsPlanService;
    }

    public function index(Request $request, $plan_id)
    {
        $payload = JwtWebFacade::getPayload();

        try {
            $platformId = $payload->platform_id;

            $recipients = $this->recipientsPlanService->getActorsRecipientsForPlans($platformId, [$plan_id]);

            return response()->json($recipients);
        } catch (ActionFailedException $e) {
            $this->customAbort('Falha ao obter informações', Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (InvalidRecipientException $e) {
            $this->customAbort('Recebedor inexistente', Response::HTTP_NOT_FOUND);
        }
    }
}
