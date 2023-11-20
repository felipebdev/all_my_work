<?php

namespace App\Services\Producer;

use App\Exceptions\InvalidOperationException;
use App\ProducerProduct;
use App\Product;
use App\Repositories\Contracts\ProducerProductRepositoryInterface;
use App\Repositories\Contracts\ProducerRepositoryInterface;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;

class ProducerProductService
{
    private ProducerRepositoryInterface $producerRepository;
    private ProducerProductRepositoryInterface $producerProductRepository;

    public function __construct(
        ProducerRepositoryInterface $producerRepository,
        ProducerProductRepositoryInterface $producerProductRepository
    ) {
        $this->producerRepository = $producerRepository;
        $this->producerProductRepository = $producerProductRepository;
    }

    public function getProducerProductReport(string $platformId, int $producerId): Builder
    {
        $producer = $this->producerRepository->findById($producerId);
        if ($producer->platform_id != $platformId) {
            throw new InvalidOperationException(['Coprodutor não encontrado na plataforma']);
        }

        return $this->producerProductRepository->reportProducerProducts($producer->id);
    }

    /**
     * Add a product to producer, checking business rules
     *
     * @param  string  $platformId
     * @param  int  $producerId
     * @param  int  $productId
     * @param  float  $percent
     * @param  \DateTimeInterface|null  $contractLimit
     * @return \App\ProducerProduct
     * @throws \Exception
     */
    public function addProductToProducer(
        string $platformId,
        int $producerId,
        int $productId,
        float $percent,
        ?DateTimeInterface $contractLimit
    ): ProducerProduct {
        $this->validateAddProductToProducer($producerId, $platformId, $productId, $percent, $contractLimit);

        $result = $this->producerProductRepository->baseCreate([
            'producer_id' => $producerId,
            'product_id' => $productId,
            'percent' => $percent,
            'contract_limit' => $contractLimit
        ]);

        return $result;
    }

    /**
     * Cancel product from producer checking if given platform matches
     *
     * @param  string  $platformId
     * @param  int  $productProducerId
     * @return bool
     */
    public function cancelProducerProduct(string $platformId, int $productProducerId): bool
    {
        $productProducer = $this->producerProductRepository->findById($productProducerId);
        if ($productProducer->producer->platform_id != $platformId) {
            throw new InvalidOperationException(['Produto não encontrado na plataforma']);
        }

        return $this->producerProductRepository->cancelProducerProduct($productProducer->id);
    }

    /**
     * @param  int  $producerId
     * @param  string  $platformId
     * @param  int  $productId
     * @param  float  $percent
     * @param  \DateTimeInterface|null  $contractLimit
     * @throws \App\Exceptions\InvalidOperationException
     */
    private function validateAddProductToProducer(
        int $producerId,
        string $platformId,
        int $productId,
        float $percent,
        ?DateTimeInterface $contractLimit
    ): void {
        $errors = [];
        $platformUser = $this->producerRepository->getPlatformUserByProducerId($producerId);
        if ($platformUser->platform_id != $platformId) {
            $errors[] = 'Coprodutor não encontrado na plataforma';
        }

        $product = Product::findOrFail($productId);
        if ($product->platform_id != $platformId) {
            $errors[] = 'Produto não encontrado na plataforma';
        }

        if ($percent < 0.1) {
            $errors[] = "Valor mínimo de 0.1 (fornecido $percent)";
        } elseif ($percent > 90) {
            $errors[] = "Valor máximo de 90 (fornecido $percent)";
        }

        if ($contractLimit != null && $contractLimit < Carbon::now()->setTime(0, 0)) {
            $errors[] = 'Data deve ser hoje ou posterior';
        }

        if ($this->producerProductRepository->hasActiveContract($producerId, $productId)) {
            $errors[] = 'Coprodutor já possui produto vinculado';
        }

        $currentTotal = $this->producerProductRepository->totalPercentWithActiveContracts($productId);
        if ($currentTotal + $percent > 90) {
            $errors[] = 'Limite de 90% entre os coprodutores';
        }

        if ($errors) {
            throw new InvalidOperationException($errors);
        }
    }

}
