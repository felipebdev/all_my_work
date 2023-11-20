<?php

namespace App\Services\Checkout;

use Illuminate\Support\Facades\Auth;
use App\Client;
use App\Platform;
use App\Http\Traits\CustomResponseTrait;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;

class RecipientsService
{
    use CustomResponseTrait;

    private CheckoutBaseService $checkoutBaseService;

    public function __construct(CheckoutBaseService $checkoutBaseService)
    {
        $this->checkoutBaseService = $checkoutBaseService;
    }

    public function createProducerRecipient(String $platformId, String $actingAs): array
    {
        try {
            $correlationId = (string) Uuid::uuid4();

            Log::info('creating producer recipient on checkout', [
                'user_id' => Auth::user()->id ?? null,
                'user_email' => Auth::user()->email ?? null,
                'platform_id' => $platformId ?? null,
                'acting_as' => $actingAs ?? null,
                'correlation_id' => $correlationId ?? null,
            ]);

            $this->checkoutBaseService->withCorrelationId($correlationId);

            $req = $this->checkoutBaseService->connectionConfig($platformId, Auth::user()->id, [
                'acting_as' => $actingAs,
            ]);
            $res = $req->post('recipients');
            $stream = $res->getBody();
            return json_decode($stream->getContents(), true);
        } catch (BadResponseException $e) {
            throw new \Exception(\json_decode($e->getResponse()->getBody()->getContents())->message, 422);
        }

    }
}
