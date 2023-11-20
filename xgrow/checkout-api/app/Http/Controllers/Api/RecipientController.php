<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\Finances\RecipientAlreadyExistsException;
use App\Exceptions\RecipientFailedException;
use App\Facades\JwtWebFacade;
use App\Http\Controllers\Controller;
use App\Http\Traits\CustomResponseTrait;
use App\Services\Finances\Recipient\RecipientManagerService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RecipientController extends Controller
{

    use CustomResponseTrait;

    private RecipientManagerService $recipientManagerService;

    public function __construct(RecipientManagerService $recipientManagerService)
    {
        $this->recipientManagerService = $recipientManagerService;
    }

    public function store(Request $request)
    {
        $payload = JwtWebFacade::getPayload();

        $platformId = $payload->platform_id;
        $userId = $payload->user_id;
        $actingAs = $payload->acting_as;

        try {
            $recipient = $this->recipientManagerService->createRecipientAndStore($platformId, $userId, $actingAs);

            if (!$recipient->canTransact()) {
                $this->customAbort('Recebedor criado, mas com problema', Response::HTTP_SERVICE_UNAVAILABLE);
            }

            return response()->json($recipient);
        } catch (RecipientAlreadyExistsException $e) {
            $this->customAbort('Recebedor já criado na plataforma', Response::HTTP_CONFLICT);
        } catch (RecipientFailedException $e) {
            if (app()->bound('sentry')) {
                app('sentry')->captureException($e);
            }
            $this->customAbort('Falha ao criar recebedor: ' . $e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
