<?php


namespace App\Services\Mundipagg\Objects;

#[Immutable]
class ProducerShare
{
    public $producerProductId;
    public $producerId;
    public $planId;
    public $productId;
    public $percent;
    public $amount;
    public $anticipation;

    public function __construct($producerProductId, $producerId, $planId, $productId, $percent, $amount, $anticipation)
    {
        $this->producerProductId = $producerProductId;
        $this->producerId = $producerId;
        $this->planId = $planId;
        $this->productId = $productId;
        $this->percent = $percent;
        $this->amount = $amount;
        $this->anticipation = $anticipation;
    }


}
