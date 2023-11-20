<?php

namespace App\Repositories\Affiliation;

use App\Exceptions\ConflictException;
use App\Exceptions\NotFoundException;
use App\Plan;
use App\PlatformUser;
use App\Producer;
use App\Services\Affiliation\Objects\AffiliateCreation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AffiliateRepository
{
    /**
     * Get an affiliate in given platform by email
     *
     * @param  string  $platformId
     * @param  string  $email
     * @return \App\Producer The affiliate
     * @throws \App\Exceptions\ConflictException Trows exception if is a Producer
     * @throws \App\Exceptions\NotFoundException Trows exception if is not found
     */
    public function getAffiliateByPlatformAndEmail(string $platformId, string $email): Producer
    {
        $producers = $this->getProducersInPlatform($platformId, $email);

        $isProducerInPlatform = $producers->contains('type', Producer::TYPE_PRODUCER);
        if ($isProducerInPlatform) {
            throw new ConflictException('Affiliate is a producer in this platform');
        }

        $affiliateInPlatform = $producers->where('type', Producer::TYPE_AFFILIATE)->first();

        if ($affiliateInPlatform) {
            return $affiliateInPlatform;
        }

        throw new NotFoundException('Affiliate not found on this platform');
    }

    /**
     * Check if producer has an affiliation with this plan
     *
     * @param  \App\Producer  $affiliate
     * @param  string  $planId
     * @return bool true if producer has an affiliation with this plan, false otherwise
     */
    public function affiliateHasPlan(Producer $affiliate, string $planId): bool
    {
        $plan = Plan::findOrFail($planId);

        return $affiliate->producerProduct()
            ->where('product_id', $plan->product->id)
            ->exists();
    }

    /**
     * Create an affiliate on the platform.
     *
     * @param  string  $platformId
     * @param  \App\Services\Affiliation\Objects\AffiliateCreation  $affiliateCreation
     * @return \App\Producer
     */
    public function createAffiliate(string $platformId, AffiliateCreation $affiliateCreation): Producer
    {
        $platformUser = $this->getPlatformUserOrCreate(
            $affiliateCreation->email,
            $platformId,
            $affiliateCreation->name
        );

        try {
            $producers = $this->getProducersInPlatform($platformUser->id, $affiliateCreation->email);
        } catch (NotFoundException $e) {
            $producers = new Collection();
        }

        if ($producers->count() > 0) {
            $affiliate = $producers->first()->replicate();
            $affiliate->type = Producer::TYPE_AFFILIATE;
            $affiliate->platform_id = $platformId;
        } else {
            $affiliate = new Producer();
            $affiliate->type = Producer::TYPE_AFFILIATE;
            $affiliate->platform_id = $platformId;
            $affiliate->platform_user_id = $platformUser->id;
            $affiliate->accepted_terms = 1;
            $affiliate->document_type = $affiliateCreation->documentType;
            $affiliate->document = $affiliateCreation->documentNumber;
            $affiliate->holder_name = $affiliateCreation->legalName;
            $affiliate->account_type = $affiliateCreation->accountType;
            $affiliate->bank = $affiliateCreation->bankCode;
            $affiliate->branch = $affiliateCreation->agency;
            $affiliate->branch_check_digit = $affiliateCreation->agencyDigit;
            $affiliate->account = $affiliateCreation->account;
            $affiliate->account_check_digit = $affiliateCreation->accountDigit;
            $affiliate->document_verified = 1;
        }

        $affiliate->save();

        return $affiliate;
    }

    /**
     * @param  string  $platformId
     * @param  string  $planId
     * @return \Illuminate\Support\Collection<Producer>
     */
    public function listPlanAffiliates(string $platformId, string $planId): Collection
    {
        $plan = Plan::findOrFail($planId);

        $productId = $plan->product->id;

        $affiliates = Producer::where('type', Producer::TYPE_AFFILIATE)
            ->where('platform_id', $platformId)
            ->whereHas('producerProduct', function ($query) use ($productId) {
                $query->where('product_id', $productId);
            })
            ->with('producerProduct')
            ->get();

        return $affiliates;
    }

    /**
     * List all Producers in a platform (including Affiliates)
     *
     * @param  string  $platformId
     * @param  string  $email
     * @return \Illuminate\Support\Collection
     * @throws \App\Exceptions\NotFoundException
     */
    private function getProducersInPlatform(string $platformId, string $email): Collection
    {
        $platformUser = PlatformUser::where('email', $email)->first();

        if (!$platformUser) {
            return new Collection();
        }

        $producers = $platformUser->producers->where('platform_id', $platformId);

        if (!$producers) {
            throw new NotFoundException('No producers found on platform');
        }

        return $producers;
    }

    /**
     * Get a Platform User by email or create a new one.
     *
     * @param  string  $email
     * @param  string  $platformId
     * @param  string  $name
     * @return \App\PlatformUser
     */
    private function getPlatformUserOrCreate(string $email, string $platformId, string $name): PlatformUser
    {
        $platformUser = PlatformUser::firstOrCreate(
            [
                'email' => $email,
            ],
            [
                'name' => $name,
                'password' => Hash::make(Str::random(8)),
                'platform_id' => $platformId,
                'active' => true,
                'display_name' => $name,
            ]
        );

        return $platformUser;
    }


}
