<?php

namespace App\Services\Mundipagg\Objects;

use MundiAPILib\Models\GetOrderResponse as MundipaggGetOrderResponse;
use PagarmeCoreApiLib\Models\GetOrderResponse as PagarmeGetOrderResponse;

/**
 * Class OrderResult stores information about order
 *
 * @package App\Services\Mundipagg\Objects
 */
#[Immutable]
class OrderResult
{
    private ?MundipaggGetOrderResponse $mundipaggOrderResponse = null;
    private ?PagarmeGetOrderResponse $pagarmeOrderResponse = null;

    /**
     * Split is absent on test charge
     *
     * @var \App\Services\Mundipagg\Objects\ProducerSplitResult[]|null
     */
    private ?array $producerSplits = null;

    protected function __construct()
    {
        // prevent direct instantiation
    }

    public static function empty()
    {
        return new self();
    }

    public static function fromMundipagg(MundipaggGetOrderResponse $orderResponse, ?array $producerSplits = null)
    {
        $self = new self();
        $self->mundipaggOrderResponse = $orderResponse;
        $self->producerSplits = $producerSplits;
        return $self;
    }

    public static function fromPagarme(PagarmeGetOrderResponse $orderResponse, ?array $producerSplits = null)
    {
        $self = new self();
        $self->pagarmeOrderResponse = $orderResponse;
        $self->producerSplits = $producerSplits;
        return $self;
    }

    #[Pure]
    /**
     * @return \MundiAPILib\Models\GetOrderResponse|null
     */
    public function getMundipaggOrderResponse(): ?MundipaggGetOrderResponse
    {
        return $this->mundipaggOrderResponse;
    }

    #[Pure]
    /**
     * @return \PagarmeCoreApiLib\Models\GetOrderResponse|null
     */
    public function getPagarmeOrderResponse(): ?PagarmeGetOrderResponse
    {
        return $this->pagarmeOrderResponse;
    }

    #[Pure]
    /**
     * Return OrderResponse from Mundipagg/Pagar.me
     * @note This is an UNSAFE method that can return multiple types
     *
     * @return \MundiAPILib\Models\GetOrderResponse|\PagarmeCoreApiLib\Models\GetOrderResponse|null
     */
    public function getOrderResponse()
    {
        return $this->mundipaggOrderResponse ?? $this->pagarmeOrderResponse ?? null;
    }

    /**
     * @return \App\Services\Mundipagg\Objects\ProducerSplitResult[]|null
     */
    #[Pure]
    public function getProducerSplits(): ?array
    {
        return $this->producerSplits;
    }

}
