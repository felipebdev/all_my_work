<?php

namespace App\Http\Controllers\Api;

use App\Config;
use App\Http\Controllers\Controller;
use App\Http\Requests\ConfigRequest;
use App\Http\Traits\CustomResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConfigController extends Controller
{
    use CustomResponseTrait;

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @param Config $config
     */
    public function __construct(Config $config){
        $this->config = $config;
    }

    /**
     * Show platform setting
     * @return JsonResponse
     */
    public function getConfig()
    {
        try {
            $config = $this->config->first();
            return $this->customJsonResponse(
                'Dados de configuração.',
                200,
                ['config' => $config]
            );
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }

    public function update(Request $request){
        try {
            $config = $this->config->first();

            $config->bank = $request->bank;
            $config->branch = $request->branch;
            $config->account = $request->account;
            $config->name = $request->name;
            $config->email = $request->email;
            $config->document = $request->document;
            $config->save();

            return $this->customJsonResponse(
                'Dados de configuração atualizados com sucesso.',
                200,
                ['config' => $config]
            );
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }

}
