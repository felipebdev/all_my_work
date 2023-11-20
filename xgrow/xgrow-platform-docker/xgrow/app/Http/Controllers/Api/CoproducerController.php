<?php

namespace App\Http\Controllers\Api;

use App\Helpers\CollectionHelper;
use App\Http\Controllers\Controller;
use App\Http\Traits\CustomResponseTrait;
use App\Repositories\Producers\ProducerProductRepository;
use App\Services\Auth\ClientStatus;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use stdClass;

class CoproducerController extends Controller
{
    use CustomResponseTrait;

    private ProducerProductRepository $producerProductRepository;

    public function __construct(ProducerProductRepository $producerProductRepository)
    {
        $this->producerProductRepository = $producerProductRepository;
    }

    public function index() {
        $status = ClientStatus::withPlatform(Auth::user()->platform_id, Auth::user()->email);

        return view('coproducer.index', [
            'clientApproved' => $status->clientApproved,
        ]);
    }

    /**
     * Get all coproducers
     * @param Request $request
     * @param $productId
     * @return JsonResponse
     */
    public function getProducersByProductId(Request $request, $productId): JsonResponse
    {
        try {
            $offset = $request->input('offset') ?? 25;
            $searchTerm = $request->input('search') ?? null;
            $searchStatus = $request->input('status') ?? null;

            $producers = $this->producerProductRepository->getProducers($productId, $searchTerm, $searchStatus)->get();

            $data = CollectionHelper::paginate($producers, $offset);

            return $this->customJsonResponse('Dados carregados com sucesso.', 200, ['producers' => $data]);
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }

    /** Add new coproducer
     * @param Request $request
     * @param $productId
     * @return JsonResponse
     */
    public function store(Request $request, $productId): JsonResponse
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'name' => 'required',
                'commission' => 'required',
            ]);

            $data = new stdClass();
            $data->email = $request->input('email');
            $data->product_id = $productId;
            $data->contract_limit = $request->input('due') ?? null;
            $data->percent = $request->input('commission');
            $this->producerProductRepository->storeProducer($data);

            return $this->customJsonResponse('Coprodutor adicionado com sucesso.', 201);
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }

    /** Update coproducer data
     * @param Request $request
     * @param $productId
     * @return JsonResponse
     */
    public function update(Request $request, $productId): JsonResponse
    {
        try {
            $request->validate([
                'commission' => 'required',
            ]);

            $data = new stdClass();
            $data->contract_limit = $request->input('due') ?? null;
            $data->percent = $request->input('commission');
            $data->producer_id = $request->input('producer');
            $data->product_id = $productId;

            $this->producerProductRepository->updateProducer($data);

            return $this->customJsonResponse('Coprodutor atualizado com sucesso.', 200);
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }

    /** Cancel coproducer Contract
     * @param Request $request
     * @param $productId
     * @return JsonResponse
     */
    public function cancelContract(Request $request, $productId): JsonResponse
    {
        try {
            $productProducerId = $request->input('productProducerId');
            if (!$productProducerId)
                throw new Exception('Numero do contrato não informado.', 400);
            $this->producerProductRepository->cancelContract($productProducerId);

            return $this->customJsonResponse('Contrato cancelado com sucesso.', 200);
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }
}
