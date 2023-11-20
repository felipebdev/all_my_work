<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Http\Traits\CustomResponseTrait;
use App\Repositories\MobileConfiguration\MobileConfigurationRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MobileConfigurationController extends Controller
{

    use CustomResponseTrait;

    protected MobileConfigurationRepository $mobileConfigurationRepository;

    public function __construct(MobileConfigurationRepository $mobileConfigurationRepository){
        $this->mobileConfigurationRepository = $mobileConfigurationRepository;
    }


    public function show(){
        try {
            $notification = $this->mobileConfigurationRepository->show();
            return $this->customJsonResponse(
                'Preferências do usuário.',
                200,
                ['notifications' => $notification]
            );
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function save(Request $request){

        $request->validate([
            'notifications' => 'required|boolean',
            'notificationsSells' => 'required|boolean',
            'notificationsSellsProductName' => 'required|boolean',
        ]);

        try {
            $this->mobileConfigurationRepository->save($request->all());
            return $this->customJsonResponse(
                'Dados atualizados com sucesso.',
                200
            );
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }
}
