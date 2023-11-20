<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\APIController;
use App\Services\ChargebackService;
use Illuminate\Http\Request;
use App\Http\Traits\CustomResponseTrait;

class ChargebackController extends APIController
{
    use CustomResponseTrait;

    private ChargebackService $chargebackService;

    public function __construct(ChargebackService $chargebackService)
    {
        parent::__construct();
        $this->chargebackService = $chargebackService;
    }

    public function create(){
        return view('chargeback.create');
    }

    public function store(Request $request){

        if ($request->file('file')) {

            if ($link = $this->chargebackService->getLink($request->file('file'))) {

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
