<?php

namespace App\Repositories\Producers;

use App\Mail\SendEmailInvitationToBeCoProducer;
use App\PlatformUser;
use App\Producer;
use App\ProducerProduct;
use App\Repositories\BaseRepository;
use App\Repositories\Contracts\ProducerProductRepositoryInterface;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ProducerProductRepository extends BaseRepository implements ProducerProductRepositoryInterface
{

    public function model()
    {
        return ProducerProduct::class;
    }

    public function reportProducerProducts(int $producerId): Builder
    {
        $query = $this->model
            ->select()
            ->selectRaw('producer_products.id AS producer_products_id')
            ->selectRaw('producer_products.producer_id AS producer_products_producer_id')
            ->selectRaw('producer_products.status AS producer_products_status')
            ->selectRaw('producer_products.product_id AS producer_products_product_id')
            ->selectRaw('producer_products.percent AS producer_products_percent')
            ->selectRaw('producer_products.contract_limit AS producer_products_contract_limit')
            ->selectRaw('producer_products.canceled_at AS producer_products_canceled_at')
            ->selectRaw('products.name AS products_name')
            ->leftJoin('producers', 'producers.id', '=', 'producer_products.producer_id')
            ->leftJoin('products', 'products.id', '=', 'producer_products.product_id')
            ->where('producer_id', $producerId);

        return $query;
    }

    /**
     * @param int $producerProductId
     * @return bool true if successfully canceled, false otherwise
     * @throw \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function cancelProducerProduct(int $producerProductId): bool
    {
        $producerProduct = $this->findById($producerProductId);
        $producerProduct->status = ProducerProduct::STATUS_CANCELED;
        $producerProduct->canceled_at = new \DateTimeImmutable();
        return $producerProduct->save();
    }

    public function hasActiveContract(int $producerId, int $productId): bool
    {
        return $this->model
            ->where('producer_id', $producerId)
            ->where('product_id', $productId)
            ->where('status', ProducerProduct::STATUS_ACTIVE)
            ->whereRaw('(contract_limit >= CURDATE() OR contract_limit IS null)')
            ->exists();
    }

    public function listActiveContracts(int $productId): Collection
    {
        return $this->model
            ->where('product_id', $productId)
            ->where('status', ProducerProduct::STATUS_ACTIVE)
            ->whereRaw('(contract_limit >= CURDATE() OR contract_limit IS null)')
            ->get();
    }

    public function totalPercentWithActiveContracts(int $productId): float
    {
        return $this->model
            ->where('product_id', $productId)
            ->where('status', ProducerProduct::STATUS_ACTIVE)
            ->whereRaw('(contract_limit >= CURDATE() OR contract_limit IS null)')
            ->sum('percent');
    }

    /**
     * @param int $producerId
     * @return \Illuminate\Support\Collection
     */
    public function listProducerProductsByProducerId(int $producerId): Collection
    {
        return $this->model->where('producer_id', $producerId)->with('product')->get();
    }


    /** Get Producer with search term
     * @param $productId
     * @param null $term
     * @param null $status
     * @return Builder
     */
    public function getProducers($productId, $term = null, $status = null): Builder
    {
        $query = ProducerProduct::select([
            'producer_products.id as ppId',
            'producer_products.contract_limit as contract_limit',
            'producer_products.created_at as created_at',
            'producer_products.percent as percent',
            'producer_products.status as status',
            'producer_products.canceled_at as canceled_at',
            'producer_products.split_invoice as split_invoice',
            'producers.id as id',
            'platforms_users.name as name',
            'platforms_users.email as email',
        ])
            ->leftJoin('producers', 'producer_products.producer_id', '=', 'producers.id')
            ->leftJoin('platforms_users', 'producers.platform_user_id', '=', 'platforms_users.id')
            ->where('producer_products.product_id', $productId)
            ->where('producers.type', 'P');

        if ($term) {
            $query->where(function (Builder $q) use ($term) {
                return $q
                    ->where('platforms_users.name', 'like', "%$term%")
                    ->orWhere('platforms_users.email', 'like', "%$term%");
            });
        }

        if ($status) {
            $query->where('producer_products.status', "$status");
        }

        return $query;
    }

    /** Save a coproducer on platform
     * @param $data
     * @return mixed
     * @throws Exception
     */
    public function storeProducer($data)
    {
        $platformUser = PlatformUser::where('email', $data->email)->first();

        /** Verify if coproducer has on platform */
        if (!$platformUser)
            throw new Exception('O coprodutor precisa ser um usuário da plataforma.', 400);

        /** Verify max % for divide */
        $totalPercent = ProducerProduct::where([
            ['product_id', '=', $data->product_id],
            ['canceled_at', '=', null]
        ])->whereHas('producer', function (Builder $query) {
            $query->where('type', Producer::TYPE_PRODUCER);
        })->sum('percent');

        if ($totalPercent + $data->percent > 80)
            throw new Exception('Limite de 80% entre os coprodutores.', 400);

        /** Verify if not same platform user is sent invite*/
        if (Auth::user()->email === $data->email)
            throw new Exception("Você não pode ser coprodutor do mesmo produto.", 400);

        /** Verify if coproducer exist on this product */
        $hasProducer = Producer::where('platform_user_id', $platformUser->id)->orderBy('created_at', 'desc')->first();
        if ($hasProducer) {
            $hasProductForProducer = ProducerProduct::where([
                ['producer_id', '=', $hasProducer->id],
                ['product_id', '=', $data->product_id],
                ['canceled_at', '=', null]
            ])->first();
            if ($hasProductForProducer)
                throw new Exception('Você já adicionou esse coprodutor a esse produto.', 400);
        }

        $is_affiliate = Producer::where([ ['platform_user_id', $platformUser->id], ['platform_id', Auth::user()->platform_id], ['producers.type', 'A'] ])->first();
        if ($is_affiliate)
            throw new Exception('Você não pode ser co-produtor e afiliado na mesma plataforma!', 400);

        $checkProducer = Producer::where('platform_id', Auth::user()->platform_id)->where('platform_user_id', $platformUser->id)->first();

        if (!$checkProducer) {
            $producer = Producer::insertGetId([
                'platform_id' => Auth::user()->platform_id,
                'platform_user_id' => $platformUser->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        } else {
            $producer = $checkProducer->id;
        }

        $producerProduct = ProducerProduct::insertGetId([
            'producer_id' => $producer,
            'product_id' => $data->product_id,
            'contract_limit' => $data->contract_limit,
            'percent' => $data->percent,
            'split_invoice' => $data->split_invoice,
            'status' => 'pending',
            'canceled_at' => null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $accessData = [
            'name' => $platformUser->name,
            'product_id' => $data->product_id,
        ];

        // Send mail as a last action (prevents database inconsistency due to mail provider problems)
        Mail::to($platformUser->email)->send(new SendEmailInvitationToBeCoProducer($accessData));

        return $producerProduct;
    }

    /** Update a coproducer on platform
     * @param $data
     * @return mixed
     * @throws Exception
     */
    public function updateProducer($data)
    {
        /** Verify if coproducer exist on this product */
        $hasProductForProducer = ProducerProduct::where([
            ['producer_id', '=', $data->producer_id], ['product_id', '=', $data->product_id]
        ])->first();

        /** Verify max % for divide */
        $totalPercent = ProducerProduct::where('product_id', $data->product_id)
            ->where('status', '!=', 'canceled')
            ->whereHas('producer', function (Builder $query) {
                $query->where('type', Producer::TYPE_PRODUCER);
            })
            ->sum('percent');
        if (($totalPercent + $data->percent - $hasProductForProducer->percent) > 80)
            throw new Exception('Limite de 80% entre os coprodutores.', 400);

        $hasProductForProducer->contract_limit = $data->contract_limit;
        $hasProductForProducer->percent = $data->percent;
        $hasProductForProducer->split_invoice = $data->split_invoice;
        $hasProductForProducer->updated_at = Carbon::now();
        $hasProductForProducer->save();

        return $hasProductForProducer;
    }

    /** Cancel coproducer Contract
     * @param $productProducerId
     * @return mixed
     */
    public function cancelContract($productProducerId)
    {
        /** Verify if Contract exist on this product */
        $hasProductForProducer = ProducerProduct::findOrFail($productProducerId);

        $hasProductForProducer->canceled_at = Carbon::now();
        $hasProductForProducer->status = 'canceled';
        $hasProductForProducer->save();

        return $hasProductForProducer;
    }
}
