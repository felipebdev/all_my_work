<?php

namespace App\Http\Controllers\Api;

use App\Helpers\CollectionHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\ServiceRequest;
use App\Http\Traits\CustomResponseTrait;
use App\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    use CustomResponseTrait;

    /**
     * @var Service
     */
    private Service $service;

    /**
     * @param Service $service
     * @return void
     */
    public function __construct(Service $service){
        $this->service = $service;
    }

    /**
    * List all services
    * @param Request $request
    * @return JsonResponse
    */
    public function index(Request $request){
        try {

            $offset = $request->input('offset') ?? 25;

            $services = $this->service
                ->orderBy('price')
                ->get();

            return $this->customJsonResponse(
                'Dados carregados com sucesso.',
                200,
                ['services' => CollectionHelper::paginate($services, $offset)]
            );

        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }

    /**
     * Show service data
     * @param $id
     * @return JsonResponse
     */
    public function show($id)
    {
        try {
            $service = $this->service->find($id);
            return $this->customJsonResponse(
                'Dados da assinatura.',
                200,
                ['user' => $service]
            );
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }

    /**
     * Save service
     * @param ServiceRequest $request
     * @return JsonResponse
     */
    public function store(ServiceRequest $request)
    {
        try
        {
            $service = $this->service->create($request->validated());
            return $this->customJsonResponse('Assinatura adicionada com sucesso.', 201, ['service' => $service]);
        }
        catch (\Exception $exception)
        {
            return $this->customJsonResponse($exception->getMessage(), 500, []);
        }
    }

    /**
     * Update service
     * @param ServiceRequest $request
     * @param $uuid
     * @return JsonResponse
     */
    public function update(ServiceRequest $request, $uuid)
    {
        try
        {
            $service = $this->service->findOrFail($uuid);

            $service->name = $request->input('name');
            $service->type = $request->input('type');
            $service->price = $request->input('price');
            $service->description = $request->input('description');
            $service->save();

            return $this->customJsonResponse('Assinatura atualizada com sucesso.', 201, ['service' => $service]);
        }
        catch (\Exception $exception)
        {
            return $this->customJsonResponse($exception->getMessage(), 500, []);
        }
    }

    /**
     * Delete service
     * @param $uuid
     * @return JsonResponse
     */
    public function destroy($uuid)
    {
        try {
            $service = $this->service->findOrFail($uuid);
            $service->delete();
            return $this->customJsonResponse('Serviço removido com sucesso.', 200);
        }
        catch (\Exception $exception) {
            return $this->customJsonResponse($exception->getMessage(), 400, []);
        }
    }

}
