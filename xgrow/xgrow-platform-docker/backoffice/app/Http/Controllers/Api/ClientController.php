<?php

namespace App\Http\Controllers\Api;

use App\Data\Intl;
use App\Helpers\CollectionHelper;
use App\Http\Requests\ClientRequest;
use App\Http\Traits\CustomResponseTrait;
use App\Services\Client\ClientService;
use App\Services\ReportAPI\ReportAPIService;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ClientController extends Controller
{
    use CustomResponseTrait;

    private ClientService $clientService;
    private ReportAPIService $reportAPIService;

    public function __construct(ClientService $clientService, ReportAPIService $reportAPIService)
    {
        $this->clientService = $clientService;
        $this->reportAPIService = $reportAPIService;
        Intl::ptBR();
    }

    /**
     * Get clients
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $offset = $request->offset ?? 25;
            $clients = $this->clientService->getClients($request->only('search', 'clientsId', 'period', 'clientType'));

            return $this->customJsonResponse(
                'Dados carregados com sucesso.',
                200,
                [
                    'clients' => CollectionHelper::paginate($clients, $offset)
                ]
            );
        } catch (\Exception $exception) {
            return $this->customJsonResponse($exception->getMessage(), 400, []);
        }
    }

    /**
     * Export Clients
     * @param Request $request
     * @return JsonResponse
     */
    public function exportClient(Request $request): JsonResponse
    {
        try {
            $this->clientService->exportClient($request->only('search', 'clientsId', 'type'));

            return $this->customJsonResponse(
                'Ação realizada com sucesso.',
                200,
                []
            );
        } catch (\Exception $exception) {
            return $this->customJsonResponse($exception->getMessage(), 400, []);
        }
    }

    /**
     * Export Client Platforms
     * @param int $clientId
     * @param Request $request
     * @return JsonResponse
     */
    public function exportClientPlatform(int $clientId, Request $request): JsonResponse
    {
        try {
            $this->clientService->exportClientPlatform($clientId, $request->only('search', 'type'));

            return $this->customJsonResponse(
                'Ação realizada com sucesso.',
                200,
                []
            );
        } catch (\Exception $exception) {
            return $this->customJsonResponse($exception->getMessage(), 400, []);
        }
    }

    /**
     * Show client data
     * @param $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        try {
            $client = $this->clientService->getClient($id);
            return $this->customJsonResponse(
                'Dados do cliente.',
                200,
                ['client' => $client]
            );
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }

    /**
     * Store clients
     *
     * @param ClientRequest $request
     * @return JsonResponse
     */
    public function store(ClientRequest $request): JsonResponse
    {
        try {
            $clients = $this->clientService->store($request->all(), $request->file('image'));
            return $this->customJsonResponse('Dados cadastrado com sucesso.', 201, ['client' => $clients]);
        } catch (\Exception $exception) {
            return $this->customJsonResponse($exception->getMessage(), 500, []);
        }
    }

    /**
     * Platform clients
     *
     * @param int $clientId
     * @param Request $request
     * @return JsonResponse
     */
    public function platform(int $clientId, Request $request): JsonResponse
    {
        try {
            $offset = $request->offset ?? 25;
            $client = $this->clientService->getClient($clientId, ['id', 'first_name', 'last_name', 'type_person', 'cpf', 'cnpj']);
            $platforms = $this->clientService->getPlatforms($clientId, $request->only('search'));

            return $this->customJsonResponse(
                'Dados carregados com sucesso.',
                200,
                [
                    'client' => $client,
                    'platforms' => CollectionHelper::paginate($platforms, $offset)
                ]
            );
        } catch (\Exception $exception) {
            return $this->customJsonResponse($exception->getMessage(), 400, []);
        }
    }

    /**
     * Platform products by id
     *
     * @param int $clientId
     * @param int $productId
     * @return JsonResponse
     */
    public function productById(int $clientId, int $productId): JsonResponse
    {
        try {

            $offset = $request->offset ?? 25;
            $client = $this->clientService->getClient($clientId, ['id', 'first_name', 'last_name', 'type_person', 'cpf', 'cnpj']);
            $product = $this->clientService->getProductById($productId);

            return $this->customJsonResponse(
                'Dados carregados com sucesso.',
                200,
                [
                    'client' => $client,
                    'product' => $product
                ]
            );
        } catch (\Exception $exception) {
            return $this->customJsonResponse($exception->getMessage(), 400, []);
        }
    }

    /**
     * Product clients
     *
     * @param int $clientId
     * @param Request $request
     * @return JsonResponse
     */
    public function product(int $clientId, Request $request): JsonResponse
    {
        try {
            $offset = $request->offset ?? 25;
            $client = $this->clientService->getClient($clientId, ['id', 'first_name', 'last_name', 'type_person', 'cpf', 'cnpj']);
            $products = $this->clientService->getProducts($clientId, $request->only('search', 'platform_id'));

            return $this->customJsonResponse(
                'Dados carregados com sucesso.',
                200,
                [
                    'client' => $client,
                    'products' => CollectionHelper::paginate($products, $offset)
                ]
            );
        } catch (\Exception $exception) {
            return $this->customJsonResponse($exception->getMessage(), 400, []);
        }
    }

    /**
     * Update the specified client.
     *
     * @param ClientRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(ClientRequest $request, int $id): JsonResponse
    {
        try {
            $clients = $this->clientService->update($id, $request->all(), $request->file('image'));
            return $this->customJsonResponse('Dados atualizados com sucesso.', 200, ['client' => $clients]);
        } catch (\Exception $exception) {
            return $this->customJsonResponse($exception->getMessage(), 500, []);
        }
    }

    /**
     * Remove the specified client.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->clientService->delete($id);
            return $this->customJsonResponse(
                'Cliente excluido com sucesso.',
                200,
                []
            );
        } catch (\Exception $exception) {
            return $this->customJsonResponse($exception->getMessage(), 500, []);
        }
    }

    /**
     * Summary
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function summary(Request $request): JsonResponse
    {
        try {
            $summary = $this->clientService->getClientSummary($request->only('period'));
            return $this->customJsonResponse(
                'Resumo de vendas.',
                200,
                [
                    'summary' => $summary,
                ]
            );
        } catch (\Exception $exception) {
            return $this->customJsonResponse($exception->getMessage(), 400, []);
        }
    }

    /**
     * Summary Client
     *
     * @param int $clientId
     * @param Request $request
     * @return JsonResponse
     */
    public function summaryClient(int $clientId, Request $request): JsonResponse
    {
        try {
            $client = $this->clientService->getClient($clientId, ['id', 'first_name', 'last_name', 'type_person', 'cpf', 'cnpj']);
            $summary = $this->clientService->getIndividualClientSummary($clientId, $request->only('period'));
            return $this->customJsonResponse(
                'Resumo de vendas.',
                200,
                [
                    'client' => $client,
                    'summary' => $summary,
                ]
            );
        } catch (\Exception $exception) {
            return $this->customJsonResponse($exception->getMessage(), 400, []);
        }
    }

    /**
     * Get client for select input
     * @param Request $request
     * @return JsonResponse
     */
    public function getByName(Request $request): JsonResponse
    {
        try {
            $search = $request->search ?? 'a';

            $clients = $this->clientService->getByName($search);
            return $this->customJsonResponse('Clientes encontrados', 200, ['clients' => $clients]);
        } catch (\Exception $exception) {
            return $this->customJsonResponse($exception->getMessage(), 400, []);
        }
    }

    /**
     * List clients
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function list(Request $request): JsonResponse
    {
        try {
            $clients = $this->clientService->list($request->only('search'));
            return $this->customJsonResponse(
                'Dados carregados com sucesso.',
                200,
                [
                    'clients' => $clients,
                ]
            );
        } catch (\Exception $exception) {
            return $this->customJsonResponse($exception->getMessage(), 400, []);
        }
    }

    /**
     * Get all (global) indicators from clients
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getGeneralClientStats(Request $request): JsonResponse
    {
        try {
            $period = str_replace(' ', '', $request->period);
            $req = $this->reportAPIService->getGeneralClientStats(['period' => $period]);
            return $this->customJsonResponse(
                'Dados carregados com sucesso.',
                200,
                ['stats' => $req->response->metrics]
            );
        } catch (\Exception $exception) {
            return $this->customJsonResponse($exception->getMessage(), 400, []);
        } catch (GuzzleException $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }

    /**
     * Get indicators for client
     *
     * @param Request $request
     * @param $clientId
     * @return JsonResponse
     */
    public function getClientStats(Request $request, $clientId): JsonResponse
    {
        try {
            $period = str_replace(' ', '', $request->period);
            $req = $this->reportAPIService->getClientStats(['period' => $period], $clientId);
            return $this->customJsonResponse(
                'Dados carregados com sucesso.',
                200,
                ['stats' => $req->response]
            );
        } catch (\Exception $exception) {
            return $this->customJsonResponse($exception->getMessage(), 400, []);
        } catch (GuzzleException $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }
}
