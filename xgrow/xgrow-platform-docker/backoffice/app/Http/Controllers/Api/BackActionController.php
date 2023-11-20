<?php

namespace App\Http\Controllers\Api;

use App\BackAction;
use App\Http\Controllers\Controller;
use App\Http\Traits\CustomResponseTrait;
use Exception;
use Illuminate\Http\JsonResponse;

class BackActionController extends Controller
{

    use CustomResponseTrait;
    /**
     * List actions
     * @return JsonResponse
     */
    public function list(): JsonResponse
    {
        try {
            $actions = BackAction::select('id', 'name')->get();
            return $this->customJsonResponse(
                'Dados carregados com sucesso.',
                200,
                ['actions' => $actions]
            );
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }
}
