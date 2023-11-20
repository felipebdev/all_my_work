<?php

namespace App\Repositories\ProductLinks;

use App\Plan;
use App\ProductLink;
use Illuminate\Support\Facades\Auth;

class ProductLinksRepository
{
    protected $model;

    public function __construct(ProductLink $productLinks)
    {
        $this->model = $productLinks;
    }

    public function list(int $productId): array
    {
        $data = $this->model->where('product_id', $productId)->with('plan')->get()->toArray();

        return $data;
    }

    public function listPlans(int $productId): array
    {
        $data = Plan::select('id', 'name')->where('product_id', $productId)->get()->toArray();

        return $data;
    }

    public function create(array $request): array
    {
        $platformId = isset($request['platform_id']) ? $request['platform_id'] : Auth::user()->platform_id;
        $planId = $request['plan_id'];
        $request['script_code'] = "
        <script>
             function getAffiliateParams() {
                  console.log('Xgrow Affiliate Params Loaded');
                  const affiliateId = new URLSearchParams(window.location.search).get('xa');
             return { platformId: '${platformId}', planId: '${planId}', affiliateId}
             }
        </script>
        <script src='https://afiliados.xgrow.com/scr-buyer.min.js'></script>";

        $data = $this->model->create($request);

        return ['message' => 'Link adicional criado com sucesso!', 'data' => $data];
    }

    public function update(int $id, array $request): array
    {
        $data = $this->model->find($id)->update($request);

        return ['message' => 'Link adicional atualizado com sucesso!', 'data' => $this->model->find($id)];
    }

    public function delete(int $id): array
    {
        $data = $this->model->destroy($id);

        return ['message' => 'Link adicional excluído com sucesso!', 'data' => $this->model->find($id)];
    }
}
