<?php

namespace App\Services\Affiliation;

use App\AffiliationSettings;
use App\Exceptions\Finances\RecipientAlreadyExistsException;
use App\Exceptions\NotFoundException;
use App\Exceptions\RecipientFailedException;
use App\Producer;
use App\ProducerProduct;
use App\Repositories\Affiliation\AffiliateProductRepository;
use App\Repositories\Affiliation\AffiliateRepository;
use App\Services\Affiliation\Objects\AffiliateCreation;
use App\Services\Finances\Recipient\RecipientManagerService;
use Exception;
use Illuminate\Support\Facades\Log;

class AffiliateService
{
    private AffiliationService $affiliationService;
    private AffiliateRepository $affiliateRepository;
    private AffiliateProductRepository $affiliateProductRepository;
    private RecipientManagerService $recipientManagerService;

    public function __construct(
        AffiliationService $affiliationSettings,
        AffiliateRepository $affiliateRepository,
        AffiliateProductRepository $affiliateProductRepository,
        RecipientManagerService $recipientManagerService
    ) {
        $this->affiliationService = $affiliationSettings;
        $this->affiliateRepository = $affiliateRepository;
        $this->affiliateProductRepository = $affiliateProductRepository;
        $this->recipientManagerService = $recipientManagerService;
    }

    /**
     * @param  string  $platformId
     * @param  string  $planId
     * @param  \App\Services\Affiliation\Objects\AffiliateCreation  $affiliateCreation
     * @return \App\ProducerProduct
     * @throws \App\Exceptions\BadConfigurationException
     * @throws \App\Exceptions\ConflictException
     * @throws \App\Exceptions\RecipientFailedException
     */
    public function storeNewAffiliate(
        string $platformId,
        string $planId,
        AffiliateCreation $affiliateCreation
    ): ProducerProduct {
        $affiliate = $this->getAffiliateOrCreate($platformId, $affiliateCreation);

        $this->updateAffiliate($affiliate, $affiliateCreation);

        if ($this->affiliateRepository->affiliateHasPlan($affiliate, $planId)) {
            return $this->affiliateProductRepository
                ->getAffiliateActiveContractsByPlanIds($affiliate->id, [$planId])
                ->first();
        }

        try {
            $this->recipientManagerService->createProducerRecipientAndStore($affiliate);
        } catch (RecipientAlreadyExistsException $e) {
            Log::warning('Producer recipient already exists, creation not required', [
                'affiliate_id' => $affiliate->id ?? null,
            ]);
        } catch (RecipientFailedException $e) {
            Log::error('Failed to store a new affiliate', [
                'exception_message' => $e->getMessage(),
            ]);
            throw $e;
        }

        $affiliationSettings = $this->affiliationService->getAffiliationSettings($platformId, $planId);

        if (!$affiliationSettings) {
            $message = 'Affiliation settings not found for plan';

            Log::error($message, [
                'plan_id' => $planId,
            ]);

            throw new Exception("{$message}: {$planId}");
        }

        $affiliateProduct = $this->attachAffiliateToProduct($affiliate, $affiliationSettings);

        return $affiliateProduct;
    }

    /**
     * Get an affiliate in given platform by email or create a new one.
     *
     * @param  string  $platformId
     * @param  \App\Services\Affiliation\Objects\AffiliateCreation  $affiliateCreation
     * @return \App\Producer
     * @throws \App\Exceptions\ConflictException
     */
    private function getAffiliateOrCreate(string $platformId, AffiliateCreation $affiliateCreation): Producer
    {
        try {
            return $this->affiliateRepository->getAffiliateByPlatformAndEmail($platformId, $affiliateCreation->email);
        } catch (NotFoundException $e) {
            return $this->affiliateRepository->createAffiliate($platformId, $affiliateCreation);
        }
    }

    /**
     * Create an affiliation "contract".
     *
     * @param  \App\Producer  $affiliate
     * @param  \App\AffiliationSettings  $affiliationSettings
     * @return \App\ProducerProduct
     */
    private function attachAffiliateToProduct(
        Producer $affiliate,
        AffiliationSettings $affiliationSettings
    ): ProducerProduct {
        /** @var \App\ProducerProduct $affiliateProduct */
        $affiliateProduct = $affiliate->producerProduct()->create([
            'product_id' => $affiliationSettings->product_id,
            'contract_limit' => null,
            'percent' => floatval($affiliationSettings->commission),
            'status' => 'active',
        ]);

        return $affiliateProduct;
    }

    private function updateAffiliate(Producer $affiliate, AffiliateCreation $affiliateCreation): void
    {
        $affiliate->document_type = $affiliateCreation->documentType;
        $affiliate->document = $affiliateCreation->documentNumber;
        $affiliate->holder_name = $affiliateCreation->legalName;
        $affiliate->account_type = $affiliateCreation->accountType;
        $affiliate->bank = $affiliateCreation->bankCode;
        $affiliate->branch = $affiliateCreation->agency;
        $affiliate->branch_check_digit = $affiliateCreation->agencyDigit;
        $affiliate->account = $affiliateCreation->account;
        $affiliate->account_check_digit = $affiliateCreation->accountDigit;
        $affiliate->save();
    }

}
