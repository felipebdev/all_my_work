<?php

namespace App\Http\Controllers\Api;

use App\BackRole;
use App\Http\Controllers\Controller;
use App\Http\Traits\CustomResponseTrait;
use Exception;
use Illuminate\Http\JsonResponse;

class BackRoleController extends Controller
{
    use CustomResponseTrait;

    /**
     * list role
     * @return JsonResponse
     */
    public function list(): JsonResponse
    {
        try {
            $roles = BackRole::select('id', 'name')->get();
            return $this->customJsonResponse(
                'Dados carregados com sucesso.',
                200,
                ['roles' => $roles]
            );
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }
}
