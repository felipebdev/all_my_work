<?php

namespace App\Services\Finances\Recipient;

use App\Plan;
use App\Platform;
use App\Producer;
use App\ProducerProduct;
use App\Services\Finances\Recipient\Objects\RecipientResponse;
use Illuminate\Support\Collection;

class RecipientsPlanService
{
    private static $goodStatus = [
        'registration',
        'affiliation',
        'active',
        //'refused',
        //'suspended',
        //'blocked',
        //'inactive',
    ];

    private static $statusTranslation = [
        'registration' => 'registrado',
        'affiliation' => 'em afiliação',
        'active' => 'ativo',
        'refused' => 'recusado',
        'suspended' => 'suspenso',
        'blocked' => 'bloqueado',
        'inactive' => 'inativo',
    ];

    private RecipientManagerService $recipientManagerService;

    public function __construct(RecipientManagerService $recipientManagerService)
    {
        $this->recipientManagerService = $recipientManagerService;
    }

    /**
     * @param  string  $platformId
     * @param  array  $planIds
     * @return array{
     *     clients: RecipientResponse,
     *     producers: array<RecipientResponse>,
     *     affiliates: array<RecipientResponse>
     * }
     * @throws \App\Exceptions\Finances\ActionFailedException
     * @throws \App\Exceptions\Finances\InvalidRecipientException
     */
    public function getActorsRecipientsForPlans(string $platformId, array $planIds): array
    {
        $productIds = Plan::whereIn('id', $planIds)->get('product_id')->pluck('product_id')->toArray();

        $clientRecipient = $this->getClientRecipient($platformId);

        //$affiliatesRecipients = $this->getAffiliatesRecipientsForProducts($productIds);

        $producersRecipients = $this->getProducersRecipientsForProducts($productIds);

        return [
            'client' => $clientRecipient,
            'client_errors' => $this->getRecipientErrors([$clientRecipient]),
            'producers' => $producersRecipients,
            'producers_errors' => $this->getRecipientErrors($producersRecipients),
            //'affiliates' => $affiliatesRecipients,
            //'affiliates_errors' => $this->getRecipientErrors($affiliatesRecipients),
        ];
    }

    /**
     * @param  string  $platformId
     * @return \App\Services\Finances\Recipient\Objects\RecipientResponse
     * @throws \App\Exceptions\Finances\ActionFailedException
     * @throws \App\Exceptions\Finances\InvalidRecipientException
     */
    private function getClientRecipient(string $platformId)
    {
        $platform = Platform::find($platformId);

        $ownerRecipientId = $platform->recipient_id ?? $platform->client->recipient_id;

        $recipient = null;
        if (strlen($ownerRecipientId) > 0) {
            $recipient = $this->recipientManagerService->obtainRecipient($ownerRecipientId);
        }
        return $recipient;
    }

    /**
     * @param  array  $productIds
     * @return \Illuminate\Support\Collection
     * @throws \App\Exceptions\Finances\ActionFailedException
     * @throws \App\Exceptions\Finances\InvalidRecipientException
     */
    private function getProducersRecipientsForProducts(array $productIds): Collection
    {
        $producers = $this->listProducersForProducts($productIds, Producer::TYPE_PRODUCER);

        return $producers->map(function (Producer $producer) {
            $recipientId = $producer->recipient_id;
            if (is_null($recipientId)) {
                return null;
            }

            return $this->recipientManagerService->obtainRecipient($recipientId);
        });
    }

    /**
     * @param  array  $productIds
     * @return \Illuminate\Support\Collection
     * @throws \App\Exceptions\Finances\ActionFailedException
     * @throws \App\Exceptions\Finances\InvalidRecipientException
     */
    private function getAffiliatesRecipientsForProducts(array $productIds): Collection
    {
        $affiliates = $this->listProducersForProducts($productIds, Producer::TYPE_AFFILIATE);

        return $affiliates->map(function (Producer $producer) {
            return $this->recipientManagerService->obtainRecipient($producer->recipient_id);
        });
    }

    /**
     * List producers/affiliates with "contract" in given products
     *
     * @param  array  $productIds
     * @param  string  $type
     * @return \Illuminate\Support\Collection<Producer>
     */
    private function listProducersForProducts(array $productIds, string $type): Collection
    {
        return Producer::query()
            ->leftJoin('producer_products', 'producer_products.producer_id', '=', 'producers.id')
            ->whereIn('producer_products.product_id', $productIds)
            ->where('producer_products.status', ProducerProduct::STATUS_ACTIVE)
            ->whereNull('producer_products.canceled_at')
            ->where(function ($query) {
                $query->whereRaw('producer_products.contract_limit >= CURDATE()');
                $query->orWhereNull('producer_products.contract_limit');
            })
            ->where('producers.type', $type)
            ->get('recipient_id');
    }

    /**
     * @param  iterable<RecipientResponse>  $recipients
     * @return array
     */
    private function getRecipientErrors(iterable $recipients): array
    {
        $errors = [];

        foreach ($recipients as $recipient) {
            if( $recipient ) {
                $status = $recipient->getStatus();

                $isGood = in_array($status, self::$goodStatus);

                if (!$isGood) {
                    $translatedStatus = self::$statusTranslation[$status] ?? $status;

                    $errors[] = "{$recipient->getName()} está {$translatedStatus}";
                }
            }
        }

        return $errors;
    }

}
