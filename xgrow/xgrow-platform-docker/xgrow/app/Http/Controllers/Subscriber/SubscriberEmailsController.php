<?php

namespace App\Http\Controllers\Subscriber;

use App\Http\Controllers\Controller;
use App\Repositories\SubscriberEmails\SubscriberEmailsRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 *
 */
class SubscriberEmailsController extends Controller
{
    /**
     * @var SubscriberEmailsRepository
     */
    private SubscriberEmailsRepository $subscriberEmailsRepository;

    /**
     * @param SubscriberEmailsRepository $subscriberEmailsRepository
     */
    public function __construct(SubscriberEmailsRepository $subscriberEmailsRepository)
    {
        $this->subscriberEmailsRepository = $subscriberEmailsRepository;
    }

    /**
     * @param $email
     * @return JsonResponse
     */
    public function listEmailsPostmark($email): JsonResponse
    {
        try {

            $listEmail = $this->subscriberEmailsRepository->listEmailsPostmark($email);

            return response()->json(
                [
                    'message' => 'success',
                    'emails' => $listEmail
                ]
            );
        } catch (Exception $e) {
            Log::error('Erro na listagem de emails',
                [
                    'message' => $e->getMessage(),
                    'code' => $e->getCode(),
                    'line' => $e->getLine(),
                    'file' => $e->getFile()
                ]
            );
            return response()->json(['error' => true, 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function resendData($id)
    {
        try {
            $this->subscriberEmailsRepository->resendData($id);

            return response()->json([
                'status' => 'success',
                'message' => "Dados enviados com sucesso!",
            ]);
        } catch (Exception $e) {

            return response()->json(
                [
                    'status' => 'error',
                    'message' => $e->getMessage(),
                ],
                $e->getCode() === 0 ? 400 : $e->getCode()
            );
        }
    }
}
