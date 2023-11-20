<?php

namespace App\Http\Controllers\Api;

use App\Services\ChargebackService;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\APIController;
use Illuminate\Http\Request;
use App\Data\Net\HTTPResponse;
use App\Helpers\CollectionHelper;
use App\Http\Traits\CustomResponseTrait;
use Illuminate\Http\JsonResponse;

class ChargebackController extends APIController
{
    
    use CustomResponseTrait;
    
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get zip link
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {

        if ($request->file('file')) {

            if ($link = ChargebackService::getLink($request->file('file'))) {

                return $this->customJsonResponse(
                    'Arquivo gerado com sucesso.',
                    200,
                    [
                        'link' => $link
                    ]
                );

            } else {
                return $this->customJsonResponse('Não foi possível gerar o arquivo .zip', 400, []);
            }


        } else {
                return $this->customJsonResponse('Não foi possível ler o arquivo xlsx.', 400, []);
        }

    }
    
}
