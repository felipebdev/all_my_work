<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductLinksRequest;
use App\Http\Traits\CustomResponseTrait;
use App\Repositories\ProductLinks\ProductLinksRepository;
use Illuminate\Http\Request;

class ProductsLinksController extends Controller
{
    use CustomResponseTrait;

    protected  $repository;

    public function __construct(ProductLinksRepository $repository)
    {
        $this->repository = $repository;
    }

    public function list(int $productId)
    {
        try {
            $data = $this->repository->list($productId);

            return response()->json([
                'error' => false,
                'data' => $data
            ]);
        } catch (\Exception $e) {

            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }

    public function listPlans(int $productId)
    {
        try {
            $data = $this->repository->listPlans($productId);

            return response()->json([
                'error' => false,
                'data' => $data
            ]);
        } catch (\Exception $e) {

            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }

    public function create(ProductLinksRequest $request)
    {
        try {
            $data = $this->repository->create($request->all());

            return response()->json([
                'error' => false,
                'message' => $data['message'],
                'data' => $data
            ]);
        } catch (\Exception $e) {

            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }

    public function update(int $productId, ProductLinksRequest $request)
    {
        try {
            $data = $this->repository->update($productId, $request->all());

            return response()->json([
                'error' => false,
                'message' => $data['message'],
                'data' => $data
            ]);
        } catch (\Exception $e) {

            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }

    public function delete(int $id)
    {
        try {
            $data = $this->repository->delete($id);

            return response()->json([
                'error' => false,
                'message' => $data['message'],
            ]);
        } catch (\Exception $e) {

            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }
}
