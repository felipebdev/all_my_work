<?php

namespace App\Services\Mundipagg\Objects;

use MundiAPILib\Models\CreatePaymentRequest;

#[Immutable]
class PaymentData
{
    /**
     * @var ProducerSplitResult[]
     */
    private array $producerSplits = [];

    /**
     * @var CreatePaymentRequest[]
     */
    private array $paymentRequests = [];

    /**
     * @param  ProducerSplitResult[]  $producerSplits
     * @param  CreatePaymentRequest[]  $paymentRequests
     * @return static
     * @throws \Exception
     */
    #[Pure]
    public static function pack(array $producerSplits, array $paymentRequests): self {
        return new self($producerSplits, $paymentRequests);
    }

    /**
     * PaymentData constructor.
     * @param  array  $producerSplits
     * @param  array  $paymentRequests
     */
    protected function __construct(array $producerSplits, array $paymentRequests)
    {
        foreach ($producerSplits as $producerSplit) {
            if (!$producerSplit instanceof ProducerSplitResult) {
                throw new \Exception('Invalid payment type');
            }

            $this->producerSplits[] = $producerSplit;
        }

        foreach ($paymentRequests as $paymentRequest) {
            if (!$paymentRequest instanceof CreatePaymentRequest) {
                throw new \Exception('Invalid payment type');
            }

            $this->paymentRequests[] = $paymentRequest;
        }
    }

    /**
     * @return \App\Services\Mundipagg\Objects\ProducerSplitResult[]
     */
    public function getProducerSplits(): array
    {
        return $this->producerSplits;
    }

    /**
     * @return \App\Services\Mundipagg\Objects\ProducerSplitResult[]
     */
    #[Pure]
    public function getPayments(): array
    {
        return $this->paymentRequests;
    }

}
