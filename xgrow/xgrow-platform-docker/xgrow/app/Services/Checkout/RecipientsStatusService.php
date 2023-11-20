<?php

namespace App\Services\Checkout;

use Illuminate\Support\Facades\Auth;
use stdClass;

/**
 * Handles recipient information from checkout-api
 */
class RecipientsStatusService
{
    private CheckoutBaseService $checkoutBaseService;

    public function __construct(CheckoutBaseService $checkoutBaseService)
    {
        $this->checkoutBaseService = $checkoutBaseService;
    }

    /**
     * Get a list of fatal recipient errors (client + producers)
     *
     * @param  string  $planId
     * @param  string  $actingAs
     * @return array List of errors, empty array if none
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function getRecipientsPlanErrors(string $planId, string $actingAs = 'client'): array
    {
        $platformId = Auth::user()->platform_id;
        $userId = Auth::user()->id;

        $recipients = $this->getRecipientsStatus($platformId, $userId, $planId, $actingAs);

        $errors = [];

        if ($recipients->client_errors ?? []) {
            $errors = array_merge($errors, $recipients->client_errors);
        }

        if ($recipients->producers_errors ?? []) {
            $errors = array_merge($errors, $recipients->producers_errors);
        }

        return $errors;
    }

    /**
     * Get information about recipient status on a given plan
     *
     * @param  string  $platformId
     * @param  string  $userId
     * @param  string  $planId
     * @param  string  $actingAs
     * @return stdClass
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected function getRecipientsStatus(string $platformId, string $userId, string $planId, string $actingAs): stdClass
    {
        $req = $this->checkoutBaseService->connectionConfig($platformId, $userId, [
            'acting_as' => $actingAs,
        ]);

        $res = $req->get("recipients/plans/{$planId}");

        $stream = $res->getBody();

        return json_decode($stream->getContents());
    }

}
