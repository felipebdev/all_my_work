<?php

namespace App\Services\Mundipagg;

use App\Client;
use App\Config;
use App\Exceptions\RecipientFailedException;
use App\Platform;
use App\Producer;
use App\Services\Mundipagg\Objects\ProducerSplitResult;
use App\Services\Mundipagg\Objects\RecipientData;
use App\Services\MundipaggService;
use Illuminate\Support\Facades\Log;
use MundiAPILib\Models\CreateSplitOptionsRequest;
use MundiAPILib\Models\CreateSplitRequest;

/**
 * Class to convert ProducerSplitResult to Mundipagg split format
 */
class MundipaggSplitService
{
    protected Platform $platform;
    protected Client $client;

    private RecipientService $recipientService;

    private MundipaggService $mundipaggService;

    public function __construct($platform_id)
    {
        Log::withContext(['platform_id' => $platform_id]);

        $this->platform = Platform::findOrFail($platform_id);
        $this->client = Client::findOrFail($this->platform->customer_id);
        $this->recipientService = new RecipientService();
        $this->mundipaggService = app()->make(MundipaggService::class);
    }


    /**
     * Generates a Mundipagg split from a ProducerSplitResult
     *
     * @param  \App\Services\Mundipagg\Objects\ProducerSplitResult  $splitResult
     * @return array
     */
    public function generateMundipaggSplit(ProducerSplitResult $splitResult): array
    {
        $split = [];

        foreach ($splitResult->aggregateAmountByProducer() as $producerId => $producerAmount) {
            $producerRecipientId = $this->getProducerRecipientIdOrCreate($producerId);
            $split[] = $this->newFlatSplit($producerAmount, $producerRecipientId, false, false, false);
        }

        // client (owner)
        $clientRecipientId = $this->getClientRecipientIdOrCreate();
        $split[] = $this->newFlatSplit($splitResult->getFinalClientAmount(), $clientRecipientId, true, false, true);

        // affiliate split
        $affiliateId = $splitResult->getAffiliateId() ?? null;
        if ($affiliateId) {
            $affiliateRecipientId = $this->getAffiliateRecipientId($affiliateId);
            $split[] = $this->newFlatSplit($splitResult->getAffiliateFinalAmount(), $affiliateRecipientId, false, false, false);
        }

        // xgrow split
        $xgrowRecipientId = $this->getXgrowRecipientIdOrCreate();
        $split[] = $this->newFlatSplit($splitResult->getFinalXgrowAmount(), $xgrowRecipientId, false, true, false);

        return $split;
    }

    /**
     * @return mixed
     * @throws \App\Exceptions\RecipientFailedException
     *
     * @deprecated On sprint 0.34, kept as a transition rule to Unified Document Flow (XP-2804)
     * @todo Refactor this method, renaming to getClientRecipientId() and removing the recipientService dependency.
     */
    public function getClientRecipientIdOrCreate()
    {
        if (strlen($this->platform->recipient_id) > 0) {
            return $this->platform->recipient_id;
        }

        $recipient = $this->recipientService
            ->create(RecipientData::fromPlatformAndClient($this->platform, $this->client));

        Log::debug('Client recipient created on payment gateway', ['recipient' => $recipient]);

        $recipientId = $recipient->id ?? null;
        if (!$recipientId) {
            Log::warning('Recipient_id not found for client', [
                'client_id' => $this->client->id ?? null,
            ]);

            throw new RecipientFailedException('Erro ao obter recebedor (cliente)');
        }

        $this->platform->recipient_id = $recipientId;
        $this->platform->save();

        return $recipientId;
    }

    /**
     * @param $producerId
     * @return mixed
     * @throws \App\Exceptions\RecipientFailedException
     *
     * @deprecated On sprint 0.34, kept as a transition rule to Unified Document Flow (XP-2804)
     * @todo Refactor this method, renaming to getProducerRecipientId() and removing the recipientService dependency.
     */
    public function getProducerRecipientIdOrCreate($producerId)
    {
        $producer = Producer::findOrFail($producerId);

        if ($producer->recipient_id) {
            return $producer->recipient_id;
        }

        $recipient = $this->recipientService->create(RecipientData::fromProducer($producer));

        Log::debug('Producer recipient created on payment gateway', ['recipient' => $recipient]);

        $recipientId = $recipient->id ?? null;
        if (!$recipientId) {
            Log::warning('Recipient_id not found for producer', [
                'producer_id' => $producerId ?? null,
            ]);

            throw new RecipientFailedException('Erro ao obter recebedor (co-produtor)');
        }

        $pagarmeRecipientId = $this->mundipaggService->retrievePagarmeRecipientId($recipientId);

        $producer->recipient_id = $recipientId;
        $producer->recipient_pagarme = $pagarmeRecipientId;
        $producer->recipient_gateway = 'mundipagg';
        $producer->save();

        return $recipientId;
    }

    public function getAffiliateRecipientId($affiliateId)
    {
        $affiliate = Producer::findOrFail($affiliateId);

        $recipientId = $affiliate->recipient_id;
        if (!$recipientId) {
            Log::warning('Recipient_id not found for affiliate', [
                'affiliate_id' => $affiliateId ?? null,
            ]);

            throw new RecipientFailedException('Recebedor não encontrado (afiliado)');
        }

        if (!$affiliate->recipient_pagarme) {
            $pagarmeRecipientId = $this->mundipaggService->retrievePagarmeRecipientId($recipientId);
            $affiliate->recipient_pagarme = $pagarmeRecipientId;
            $affiliate->save();
        }

        return $recipientId;
    }

    /**
     * @return mixed
     * @throws \App\Exceptions\RecipientFailedException
     *
     * @deprecated On sprint 0.34, kept as a transition rule to Unified Document Flow (XP-2804)
     * @todo Refactor this method, renaming to getXgrowRecipientId() and removing the recipientService dependency.
     */
    public function getXgrowRecipientIdOrCreate()
    {
        $config = Config::first();

        if (strlen($config->recipient_id) > 0) {
            return $config->recipient_id;
        }

        $recipient = $this->recipientService->create(RecipientData::fromConfig($config));

        Log::debug('Xgrow recipient created on payment gateway', ['recipient' => $recipient]);

        $recipientId = $recipient->id ?? null;
        if (!$recipientId) {
            throw new RecipientFailedException('Erro ao obter recebedor (Xgrow)');
        }

        $pagarmeRecipientId = $this->mundipaggService->retrievePagarmeRecipientId($recipientId);

        $config->recipient_id = $recipientId;
        $config->recipient_pagarme = $pagarmeRecipientId;
        $config->save();

        return $recipientId;
    }

    private function newFlatSplit(
        int $amount,
        string $recipientId,
        bool $liable,
        bool $chargeProcessingFee,
        bool $chargeRemainderFee
    ): CreateSplitRequest {
        $options = new CreateSplitOptionsRequest();
        $options->liable = $liable;
        $options->chargeProcessingFee = $chargeProcessingFee;
        $options->chargeRemainderFee = $chargeRemainderFee;

        return (new CreateSplitRequest('flat', "$amount", $recipientId, $options));
    }

}
