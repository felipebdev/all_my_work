<?php

namespace App\Services;

use App\Config;
use App\Http\Controllers\Controller;
use App\Platform;
use App\Producer;
use App\Services\Mundipagg\ApiClient\MundipaggClient;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use MundiAPILib\Http\HttpCallBack;
use MundiAPILib\Http\HttpContext;
use MundiAPILib\Models\CreateOrderRequest;
use MundiAPILib\Models\GetCustomerResponse;

class MundipaggService extends Controller
{

    private MundipaggClient $mundipaggClient;

    public function __construct()
    {
        $this->mundipaggClient = new MundipaggClient();
    }

    /**
     * @deprecated Use {@see MundipaggClient::getClient()} or create a wrapper method in this Service
     *
     * @return \MundiAPILib\MundiAPIClient
     */
    public function getClient()
    {
        return $this->mundipaggClient->getClient();
    }

    // customer
    public function getCustomerById(string $mundipaggCustomerId): GetCustomerResponse
    {
        return $this->mundipaggClient->getClient()->getCustomers()->getCustomer($mundipaggCustomerId);
    }

    /**
     * Convert Mundigpagg Recipient ID to Pagar.me ID
     *
     * @param  string  $mundipaggRecipientId Mundipagg Recipient ID
     * @return string|null Pagar.me Recipient ID, null if not found
     */
    public function convertToPagarMeRecipientId(string $mundipaggRecipientId): ?string
    {
        $platform = Platform::where('recipient_id', $mundipaggRecipientId)->first();
        if ($platform) {
            return $this->rememberPagarmeRecipientIdFromPlatform($platform, $mundipaggRecipientId);
        }

        $producer = Producer::where('recipient_id', $mundipaggRecipientId)->first();
        if ($producer) {
            return $this->rememberPagarmeRecipientIdFromProducer($producer, $mundipaggRecipientId);
        }

        $config = Config::where('recipient_id', $mundipaggRecipientId)->first();
        if ($config) {
            return $this->rememberPagarmeRecipientIdFromConfig($config, $mundipaggRecipientId);
        }

        return $this->retrievePagarmeRecipientId($mundipaggRecipientId);
    }

    /**
     * Wrapper around "getClient()->getOrders()->createOrder()" with HTTP callback logging
     *
     * @param  \MundiAPILib\Models\CreateOrderRequest  $mundipaggOrderRequest
     * @return mixed
     * @throws \MundiAPILib\APIException
     */
    public function createClientOrder(CreateOrderRequest $mundipaggOrderRequest)
    {
        $callback = new HttpCallBack(null, function (HttpContext $httpContext) {
            // Really sensitive data, must be sent to another destination OTHER THAN COMMON LOG
            //$rawBody = $httpContext->getResponse()->getRawBody();
            //Log::debug('checkout:mundipagg:createOrder:responseBody', ['raw_body' => $rawBody]);
        });

        $this->mundipaggClient->getClient()->getOrders()->setHttpCallBack($callback);

        return $this->mundipaggClient->getClient()->getOrders()->createOrder($mundipaggOrderRequest);
    }

    public function retrievePagarmeRecipientId(string $mundipaggRecipientId): ?string
    {
        try {
            $recipient = $this->mundipaggClient->getClient()->getRecipients()->getRecipient($mundipaggRecipientId);

            $gatewayRecipients = new Collection($recipient->gatewayRecipients ?? []);

            $pagarmeRecipient = $gatewayRecipients->where('gateway', 'pagarme')->first();

            if (!$pagarmeRecipient) {
                return null;
            }

            return $pagarmeRecipient->pgid;
        } catch (Exception $e) {
            return null;
        }
    }

    private function rememberPagarmeRecipientIdFromPlatform(Platform $platform, string $mundipaggRecipientId): ?string
    {
        if ($platform->recipient_pagarme) {
            return $platform->recipient_pagarme;
        }

        $pagarmeRecipient = $this->retrievePagarmeRecipientId($mundipaggRecipientId);

        if (!$pagarmeRecipient) {
            return null;
        }

        $platform->recipient_pagarme = $pagarmeRecipient;
        $platform->save();

        return $pagarmeRecipient;
    }

    private function rememberPagarmeRecipientIdFromProducer(Producer $producer, string $mundipaggRecipientId)
    {
        if ($producer->recipient_pagarme) {
            return $producer->recipient_pagarme;
        }

        $pagarmeRecipient = $this->retrievePagarmeRecipientId($mundipaggRecipientId);

        if (!$pagarmeRecipient) {
            return null;
        }

        $producer->recipient_pagarme = $pagarmeRecipient;
        $producer->save();

        return $pagarmeRecipient;
    }

    private function rememberPagarmeRecipientIdFromConfig(Config $config, string $mundipaggRecipientId): ?string
    {
        if ($config->recipient_pagarme) {
            return $config->recipient_pagarme;
        }

        $pagarmeRecipient = $this->retrievePagarmeRecipientId($mundipaggRecipientId);

        if (!$pagarmeRecipient) {
            return null;
        }

        $config->recipient_pagarme = $pagarmeRecipient;
        $config->save();

        return $pagarmeRecipient;
    }

}
