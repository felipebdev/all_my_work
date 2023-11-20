<?php

namespace App\Services\Affiliate;

use App\Exceptions\InvalidOperationException;
use App\Exceptions\NotFoundException;
use App\PlatformUser;
use App\Producer;
use App\ProducerProduct;
use App\Product;
use App\Repositories\Affiliations\AffiliationProductRepository;
use App\Repositories\Affiliations\AffiliationsRepository;
use Illuminate\Support\Facades\Log;

/**
 * This class handles affiliation "contracts"
 */
class AffiliationContractService
{
    private AffiliationProductRepository $affiliationProductRepository;
    private AffiliationsRepository $affiliationsRepository;

    public function __construct(
        AffiliationProductRepository $affiliationProductRepository,
        AffiliationsRepository $affiliationsRepository
    ) {
        $this->affiliationProductRepository = $affiliationProductRepository;
        $this->affiliationsRepository = $affiliationsRepository;
    }

    /**
     * Cancel contract on a given platform
     *
     * @param  string  $platformId
     * @param  int  $producerProductId
     * @return bool
     * @throws \App\Exceptions\InvalidOperationException
     * @throws \App\Exceptions\NotFoundException
     */
    public function cancelContract(string $platformId, int $producerProductId): bool
    {
        Log::debug('Affiliation cancel requested', [
            'platform_id' => $platformId,
            'product_producer_id' => $producerProductId,
        ]);

        $contract = $this->affiliationProductRepository->getProducerProductById($producerProductId);

        $contractPlatformId = $contract->producer->platform_id ?? null;
        if ($contractPlatformId != $platformId) {
            throw new NotFoundException('Product affiliation not found on this platform');
        }

        if ($contract->status == ProducerProduct::STATUS_CANCELED) {
            throw new InvalidOperationException(['Affiliation is already canceled']);
        }

        return $this->affiliationProductRepository->cancelProducerProduct($contract);
    }

    public function isUserBlocked(int $userId, int $productId): bool
    {
        $product = Product::find($productId);
        $platformUser = PlatformUser::find($userId);

        $affiliate = Producer::where('platform_id', $product->platform_id)
            ->where('platform_user_id', $platformUser->id)
            ->where('type', Producer::TYPE_AFFILIATE)
            ->first();

        if (!$affiliate) {
            return false; // not even an affiliate
        }

        return $affiliate->blocked_at !== null;
    }

    /**
     * Cancel all contracts in the platform and mark affiliate as blocked
     *
     * @param  string  $platformId
     * @param  int  $producerProductId
     * @return bool
     * @throws \App\Exceptions\NotFoundException
     */
    public function blockByContract(string $platformId, int $producerProductId): bool
    {
        Log::debug('Affiliation block requested', [
            'platform_id' => $platformId,
            'product_producer_id' => $producerProductId,
        ]);

        $contract = $this->affiliationProductRepository->getProducerProductById($producerProductId);

        if (!$contract) {
            throw new NotFoundException('Product affiliation not found');
        }

        $producerId = $contract->producer_id;

        $contractPlatformId = $contract->producer->platform_id ?? null;
        if ($contractPlatformId != $platformId) {
            throw new NotFoundException('Product affiliation not found on this platform');
        }

        $contractsAffected = $this->affiliationsRepository->blockAffiliateAndContracts($producerId);

        return $contractsAffected > 0;
    }

    public function unblockByContract(string $platformId, int $producerProductId): bool
    {
        Log::debug('Affiliation unblock requested', [
            'platform_id' => $platformId,
            'product_producer_id' => $producerProductId,
        ]);

        $contract = $this->affiliationProductRepository->getProducerProductById($producerProductId);

        if (!$contract) {
            throw new NotFoundException('Product affiliation not found');
        }

        return $this->affiliationsRepository->unblockAffiliate($contract->producer_id);

    }

}
