<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ValidateDocumentsRequest;
use App\Http\Traits\CustomResponseTrait;
use App\Repositories\DocumentValidation\ValidateDocumentsRepository;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;

/**
 *
 */
class ValidateDocumentsController extends Controller
{
    use CustomResponseTrait;

    /**
     * @param  ValidateDocumentsRequest  $request
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function validateDocuments(ValidateDocumentsRequest $request): JsonResponse
    {
        try {

            $validationRepository = new ValidateDocumentsRepository;

            $validate = $validationRepository->validation($request);

            if ($validate['error'] === true) {

                return $this->customJsonResponse(
                    $validate['message'],
                    $validate['code'],
                    ['errors' => $validate['message']]
                );
            }

            return $this->customJsonResponse($validate['message'], $validate['code']);
        } catch (\Exception $e) {

            return $this->customJsonResponse('Falha ao realizar ação', 400, ['errors' => $e->getMessage()]);
        }
    }
}
