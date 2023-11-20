<?php

namespace App\Http\Controllers\Api;

use App\CacheEntry;
use App\EmailProvider;
use App\Helpers\CollectionHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\EmailProvider\EmailProviderApplyRequest;
use App\Http\Requests\EmailProvider\EmailProviderRequest;
use App\Http\Traits\CustomResponseTrait;
use App\Services\EmailProvider\EmailProviderService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;


class EmailProviderController extends Controller
{
    use CustomResponseTrait;

    private EmailProviderService $emailProvider;

    /**
     * @param EmailProviderService $emailProvider
     * @return void
     */
    public function __construct(EmailProviderService $emailProvider){

        $this->emailProvider = $emailProvider;
    }

    /**
     * List all provider providers
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {

            $offset = $request->input('offset') ?? 25;

            $providers = $this->emailProvider->getEmailProviders($request->only('search'));
            foreach ($providers as $provider) {
                $provider->service_tags = implode(', ', json_decode($provider['service_tags'] ?? '[]'));
            }

            return $this->customJsonResponse(
                'Dados carregados com sucesso.',
                200,
                [
                    'providers' => CollectionHelper::paginate($providers, $offset),
                ]
            );

        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }


    /**
     * Get data providers
     * @return JsonResponse
     */
    public function getDataProvider(){
        try {

            return $this->customJsonResponse(
                'Dados carregados com sucesso.',
                200,
                [
                    'defaultProvider' => CacheEntry::where('name', '=', 'MAIL_PROVIDER_NAME')->first()->default_value ?? null,
                    'cachedProvider' => Cache::driver('redis')->get('MAIL_PROVIDER_NAME')
                ]
            );

        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }

    /**
     * Show email providers data
     * @param $id
     * @return JsonResponse
     */
    public function show($id)
    {
        try {
            $provider = $this->emailProvider->getEmailProvider($id);
            return $this->customJsonResponse(
                'Dados do provedor de email.',
                200,
                ['provider' => $provider]
            );
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }

    /**
     *
     * Get Email Provider Drivers
     * @return JsonResponse
     */
    public function getDrivers(): JsonResponse
    {
        try {
            return $this->customJsonResponse(
                'Drivers de provedores de email.',
                200,
                ['drivers' => EmailProvider::DRIVERS]
            );
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }

    /**
     * Save email provider
     * @param EmailProviderRequest $request
     * @return JsonResponse
     */
    public function store(EmailProviderRequest $request): JsonResponse
    {
        try {

            $provider = $this->emailProvider->create($request->only([
                'name', 'description', 'from_name', 'from_address', 'driver', 'settings', 'service_tags'
            ]));

            return $this->customJsonResponse('Provedor adicionado com sucesso.', 201, ['provider' => $provider]);
        } catch (\Exception $exception) {
            return $this->customJsonResponse($exception->getMessage(), 500, []);
        }
    }

    /**
     * Update email provider
     * @param EmailProviderRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(EmailProviderRequest $request, $id): JsonResponse
    {
        try {

            $provider = $this->emailProvider->update($id, $request->only([
                'name', 'description', 'from_name', 'from_address', 'driver', 'settings', 'service_tags'
            ]));

            return $this->customJsonResponse(
                'Provedor atualizado com sucesso.', 200, [
                    'provider' => $provider
            ]);
        } catch (\Exception $exception) {
            return $this->customJsonResponse($exception->getMessage(), 500, []);
        }
    }

    /**
     * Delete email provider
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        try {
            $this->emailProvider->delete($id);
            return $this->customJsonResponse('Provedor removido com sucesso.', 200);
        }
        catch (\Exception $exception) {
            return $this->customJsonResponse($exception->getMessage(), 400, []);
        }
    }

    /**
     * Apply cache entry data
     * @param EmailProviderApplyRequest $request
     * @return JsonResponse
     */
    public function apply(EmailProviderApplyRequest $request): JsonResponse
    {
        try {

        $this->emailProvider->setEmailProviderCache($request->input('provider'));

        return $this->customJsonResponse('Cache atualizado com sucesso.', 200, []);

        }
        catch (\Exception $exception) {
            return $this->customJsonResponse($exception->getMessage(), 400, []);
        }
    }


}
