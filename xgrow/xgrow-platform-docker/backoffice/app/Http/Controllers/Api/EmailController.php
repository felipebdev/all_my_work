<?php

namespace App\Http\Controllers\Api;

use App\Data\Intl;
use App\Helpers\CollectionHelper;
use App\Http\Traits\CustomResponseTrait;
use App\Services\Email\EmailService;
use App\Http\Requests\EmailRequest;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Email;

class EmailController extends Controller
{
    use CustomResponseTrait;

    private EmailService $emailService;
    private Email $email;

    public function __construct(
        EmailService $emailService, 
        Email $email
    )
    {
        $this->emailService = $emailService;
        $this->email = $email;
        Intl::ptBR();
    }

    /**
     * List all email emails
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request) : JsonResponse
    {
        try {
            $offset = $request->offset ?? 25;
            $emails = $this->emailService->getEmails($request->only('search'));

            return $this->customJsonResponse(
                'Dados carregados com sucesso.',
                200,
                [
                    'emails' => CollectionHelper::paginate($emails, $offset)
                ]
            );
        } catch (\Exception $exception) {
            return $this->customJsonResponse($exception->getMessage(), 400, []);
        }
    }

    /**
     * Show email data
     * @param $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        try {
            $email = $this->emailService->getEmail($id);
            return $this->customJsonResponse(
                'Dados do email.',
                200,
                ['email' => $email]
            );
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }

    /**
     * Save email
     * @param EmailRequest $request
     * @return JsonResponse
     */
    public function store(EmailRequest $request): JsonResponse
    {
        try {
            $email = $this->emailService->store($request->all());
            return $this->customJsonResponse(
                'Email adicionado com sucesso.', 
                201, 
                ['email' => $email]
            );
        } catch (\Exception $exception) {
            return $this->customJsonResponse($exception->getMessage(), 500, []);
        }
    }

    /**
     * Update email data
     * @param EmailRequest $request
     * @return JsonResponse
     */
    public function update(EmailRequest $request, $id): JsonResponse
    {
        try {
            $email = $this->emailService->update($id, $request->all());
            return $this->customJsonResponse(
                'Email atualizado com sucesso.', 
                200, 
                ['email' => $email]
            );
        } catch (\Exception $exception) {
            return $this->customJsonResponse($exception->getMessage(), 500, []);
        }
    }

    /**
     * Delete email user
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        try {
            $this->emailService->delete($id);
            return $this->customJsonResponse(
                'Email excluido com sucesso.',
                200,
                []
            );
        } catch (\Exception $exception) {
            return $this->customJsonResponse($exception->getMessage(), 500, []);
        }
    }

}
