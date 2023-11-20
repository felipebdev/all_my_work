<?php

namespace App\Repositories;

use App\Plan;
use App\Services\Objects\PlanFilter;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class PlanRepository
{
    /**
     * Get Plans
     * @param PlanFilter|null $filter
     * @return Builder
     */
    public function listAll(?PlanFilter $filter = null): Builder{
        return  Plan::when($filter,function ($query, $filter) {
                return Plan::when($filter->id, function ($query, $id) {
                    $query->where('plans.id', $id);
                })
                ->when($filter->search, function ($query, $search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('plans.name', 'LIKE', "%{$search}%");
                        $q->orWhere('plans.description', 'LIKE', "%{$search}%");
                    });
                })
                ->when($filter->platformId, function ($query, $platformId) {
                    $query->where('plans.platform_id', $platformId);
                })
                ->when($filter->productId, function ($query, $productId) {
                    $query->where('plans.product_id', $productId);
                })
                ->when($filter->createdPeriod, function ($query, $periodFilter) {
                    $query->whereBetween('plans.created_at', [$periodFilter->startDate, $periodFilter->endDate]);
                });
        });
    }

    /**
     * Get products by client
     * @param PlanFilter|null $filter
     * @return Builder
     */
    public function listPlanClient(?PlanFilter $filter = null): Builder{
        return $this->listAll($filter)
            ->join('platforms', 'plans.platform_id', '=', 'platforms.id')
            ->join('clients', 'platforms.customer_id', '=', 'clients.id')
            ->when($filter->clientId, function ($query, $clientId) {
                $query->where('platforms.customer_id', $clientId);
            });
    }

    /**
     * Get products by client
     * @param PlanFilter|null $filter
     * @return Builder
     */
    public function listPlanPlatform(?PlanFilter $filter = null): Builder{
        return $this->listAll($filter)
            ->join('platforms', 'plans.platform_id', '=', 'platforms.id');
    }

    /**
     * Save Plan
     * @param array $data
     * @return void
     */
    public function create(array $data)
    {
        return Plan::insert([
            'product_id' => $data['product_id'],
            'name' => $data['name'],
            'type_plan' => $data['type_plan'],
            'price' => $data['price'],
            'payment_method_boleto' => $data['payment_method_boleto'] ?? 0,
            'payment_method_credit_card' => $data['payment_method_credit_card'] ?? 0,
            'payment_method_pix' => $data['payment_method_pix'] ?? 0,
            'payment_method_multiple_cards' => $data['payment_method_multiple_cards'] ?? 0,
            'installment' => $data['installment'],
            'checkout_layout' => $data['checkout_layout'] ?? 0,
            'checkout_address' => $data['checkout_address'] ?? 0,
            'platform_id' => $data['platform_id'],
            'status' => 0,
            'currency' => 'BRL',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }

    public function update(int $id, array $data){
        $plan = Plan::find($id);
        $plan->update([
            'name' => $data('name'),
            'type_plan' => $data('type_plan'),
            'price' => $data('price'),
            'payment_method_boleto' => $data('payment_method_boleto') ?? 0,
            'payment_method_credit_card' => $data('payment_method_credit_card') ?? 0,
            'payment_method_pix' => $data('payment_method_pix') ?? 0,
            'payment_method_multiple_cards' => $data('payment_method_multiple_cards') ?? 0,
            'installment' => $data('installment'),
            'checkout_layout' => $data('checkout_layout'),
            'checkout_address' => $data('checkout_address'),
            'platform_id' => $data('platform_id'),
            'updated_at' => Carbon::now(),
        ]);
        return $plan->refresh();
    }

    /**
     * Get Plan by ID
     *
     * @param int $id
     * @return mixed
     */
    public function findById(int $id)
    {
        return Plan::findOrFail($id);
    }

    /**
     * Delete by product id
     *
     * @param int $id
     * @return void
     */
    public function deleteByProductId(int $id)
    {
        $plans = Plan::where('product_id', $id);
        $plans->delete();
    }

}
