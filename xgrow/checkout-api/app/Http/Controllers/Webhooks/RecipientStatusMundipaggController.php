<?php

namespace App\Http\Controllers\Webhooks;

use App\Exceptions\Finances\RecipientNotFound;
use App\Exceptions\NotImplementedException;
use App\Http\Controllers\Controller;
use App\Repositories\Finances\RecipientStatusRepository;
use App\Services\Finances\Recipient\Contracts\RecipientManagerInterface;
use App\Services\Finances\Recipient\RecipientManagerAdapter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class RecipientStatusMundipaggController extends Controller
{

    private RecipientManagerInterface $recipientManager;

    private RecipientStatusRepository $recipientStatus;

    private static $allowList = [
        'affiliation',
        'acctive',
    ];

    private static $denyList = [
        'refused',
        'suspended',
        'blocked',
        'inactive',
    ];

    public function __construct(
        RecipientManagerAdapter $managerAdapter,
        RecipientStatusRepository $recipientStatusRepository
    ) {
        // use Pagarme V4 driver
        $managerDriver = $managerAdapter->driver(RecipientManagerAdapter::DRIVER_PAGARME_V4);
        if (!$managerDriver instanceof RecipientManagerInterface) {
            $message = 'Recipient manager not implemented by driver: '.$managerAdapter->getDefaultDriver();
            throw new NotImplementedException($message);
        }

        $this->recipientManager = $managerDriver;
        $this->recipientStatus = $recipientStatusRepository;
    }

    public function recipientUpdated(Request $request)
    {
        Log::info('Recipient update webhook', [
            'request' => $request->all(),
        ]);

        $requestType = $request->type ?? null;
        $mundipaggRecipientId = $request->data['id'] ?? null;
        $newStatus = $request->data['status'] ?? null;
        $registeredEmail = $request->data['email'] ?? null; // can be different from user's DB email

        Log::withContext(['request_type' => $requestType]);
        Log::withContext(['recipient_id' => $mundipaggRecipientId]);
        Log::withContext(['new_status' => $newStatus]);

        if ($requestType !== 'recipient.updated') {
            Log::info('Request type is not recipient.updated, skipping');
            return $this->fail('Somente são processados atualizações de recebedor');
        }

        try {
            $currentStatus = $this->recipientStatus->getCurrentRecipientStatus($mundipaggRecipientId);
        } catch (RecipientNotFound $e) {
            Log::error('Given recipient not found');
            return $this->fail('Não foi possível localizar o recebedor');
        }

        Log::withContext(['current_status' => $currentStatus ?? null]);

        if ($currentStatus == $newStatus) {
            Log::warning('Recipient status not changed, skipping');
            return $this->fail('Status do recebedor não foi alterado');
        }

        if (in_array($newStatus, self::$allowList)) {
            $this->handleAllowedRecipient($mundipaggRecipientId, $newStatus);
        } elseif (in_array($newStatus, self::$denyList)) {
            $this->handleDeniedRecipient($mundipaggRecipientId, $newStatus);
        } else {
            Log::error('Invalid recipient status change');
        }

        Log::debug('Postback processed successfully');

        return $this->success('Processado com sucesso');
    }

    private function handleAllowedRecipient($mundipaggRecipientId, $newStatus): void
    {
        $this->recipientStatus->updateRecipientStatus($mundipaggRecipientId, $newStatus, null);

        $this->recipientStatus->upateClientVerifiedByRecipientId($mundipaggRecipientId, true);
    }

    /**
     * @param $mundipaggRecipientId
     * @param $newStatus
     * @throws \App\Exceptions\Finances\ActionFailedException
     * @throws \App\Exceptions\Finances\InvalidRecipientException
     * @throws \App\Exceptions\Finances\RecipientNotFound
     */
    private function handleDeniedRecipient($mundipaggRecipientId, $newStatus): void
    {
        $recipient = $this->recipientManager->obtainRecipient($mundipaggRecipientId);

        $statusReason = $recipient->getRawData()['status_reason'] ?? 'null';

        $this->recipientStatus->updateRecipientStatus($mundipaggRecipientId, $newStatus, $statusReason);

        $this->recipientStatus->upateClientVerifiedByRecipientId($mundipaggRecipientId, false);
    }

    private function success(string $message): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
        ]);
    }

    private function fail(string $message, int $statusCode = Response::HTTP_BAD_REQUEST): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
        ], $statusCode);
    }


}
