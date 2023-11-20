<?php

namespace App\Http\Controllers\Api;

use App\Data\Intl;
use App\Http\Requests\Product\ChangeStatusRequest;
use App\Http\Requests\Product\IndexRequest;
use App\Http\Requests\Product\ProductRequest;
use App\Http\Traits\CustomResponseTrait;
use App\Services\Product\ProductService;
use App\Services\ReportAPI\ReportAPIService;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ProductController extends Controller
{
    use CustomResponseTrait;

    private ProductService $productService;
    private ReportAPIService $reportAPIService;

    public function __construct(
        ProductService   $productService,
        ReportAPIService $reportAPIService
    )
    {
        $this->productService = $productService;
        $this->reportAPIService = $reportAPIService;
        Intl::ptBR();
    }

    /**
     * Get Products
     * @param IndexRequest $request
     * @return JsonResponse
     */
    public function index(IndexRequest $request): JsonResponse
    {
        try {
            $offset = $request->input('offset') ?? 25;

            $products = $this->productService->getProducts($request->only('search', 'status'))->paginate($offset);
            $data = ['products' => $products];

            return $this->customJsonResponse('Dados carregados com sucesso.', 200, $data);
        } catch (\Exception $exception) {
            return $this->customJsonResponse($exception->getMessage(), 400, []);
        }
    }

    /**
     * Store the product.
     *
     * @param ProductRequest $request
     * @return JsonResponse
     */
    public function store(ProductRequest $request): JsonResponse
    {
        try {
            $product = $this->productService->create($request->all());
            $data = ['product' => $product];

            return $this->customJsonResponse('Dados cadastrado com sucesso.', 201, $data);
        } catch (\Exception $exception) {
            return $this->customJsonResponse($exception->getMessage(), 400);
        }
    }

    /**
     * Display the specified product.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $product = $this->productService->getProductById($id);
            $data = ['product' => $product];

            return $this->customJsonResponse('Dados carregados com sucesso.', 200, $data);
        } catch (\Exception $exception) {
            return $this->customJsonResponse('Dado inexistente.', 400);
        }
    }

    /**
     * Update the specified product.
     *
     * @param ProductRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(ProductRequest $request, int $id): JsonResponse
    {
        try {
            $product = $this->productService->update($id, $request->all());
            $data = ['product' => $product];

            return $this->customJsonResponse('Dados atualizados com sucesso.', 200, $data);
        } catch (\Exception $exception) {
            return $this->customJsonResponse($exception->getMessage(), 400);
        }
    }

    /**
     * Remove the specified product.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->productService->delete($id);

            return $this->customJsonResponse('Dados excluidos com sucesso.', 204);
        } catch (\Exception $exception) {
            return $this->customJsonResponse($exception->getMessage(), 500, []);
        }
    }

    /**
     * Change product status
     *
     *
     * @param ChangeStatusRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function changeStatus(ChangeStatusRequest $request, int $id): JsonResponse
    {
        try {
            $product = $this->productService->changeStatus($id, $request->all());
            $data = ['product' => $product];

            return $this->customJsonResponse('Status atualizado com sucesso.', 200, $data);
        } catch (\Exception $exception) {
            return $this->customJsonResponse($exception->getMessage(), 500, []);
        }
    }

    /**
     * list product
     *
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function list(Request $request): JsonResponse
    {
        try {
            $products = $this->productService->listProducts($request->only('platform_id', 'search'));
            return $this->customJsonResponse(
                'Dados carregados com sucesso.',
                200,
                ['products' => $products]
            );
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }

    /**
     * Get Product Summary
     * @param IndexRequest $request
     * @return JsonResponse
     */
    public function summary(IndexRequest $request): JsonResponse
    {
        try {
            $summary = $this->productService->getProductSummary();
            $data = ['summary' => $summary];

            return $this->customJsonResponse('Dados carregados com sucesso.', 200, $data);
        } catch (\Exception $exception) {
            return $this->customJsonResponse($exception->getMessage(), 400, []);
        }
    }

    /**
     * Get Transactions by ProductId
     * @param Request $request
     * @param $id
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function getTransactionsByProductId(Request $request, $id): JsonResponse
    {
        try {
            $offset = $request->input('offset') ?? 25;
            $page = $request->input('page') ?? 1;

            $query = ['page' => $page, 'offset' => $offset];

            $req = $this->reportAPIService->getTransactionsByProductId($id, $query);
            $data = ['transactions' => $req->response->transactions];

            return $this->customJsonResponse('Dados carregados com sucesso.', 200, $data);
        } catch (\Exception $exception) {
            return $this->customJsonResponse($exception->getMessage(), 400, []);
        }
    }
}
