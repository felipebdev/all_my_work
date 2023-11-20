<?php

namespace App\Services\Finances\Refund\Objects;

use App\Services\Finances\Contracts\FromArrayInterface;
use App\Services\Finances\Traits\FromArrayTrait;

final class PaymentRefund implements FromArrayInterface
{
    use FromArrayTrait {
        FromArrayTrait::fromArray as usingArray;
    }

    public string $paymentMethod;
    public string $paymentId = '';
    public string $paymentPlanId = '';
    public string $reason = '';
    public array $metadata = [];
    public bool $isPartial = false;

    public static function fromArray(array $data): self
    {
        $paymentPlanId = $data['payment_plan_id'] ?? '';
        if (strlen($paymentPlanId)> 0) {
            $data['isPartial'] = true;
        }

        return self::usingArray($data);
    }

    /**
     * Creates a new instance merging with provided values
     *
     * @param  array  $values
     * @return $this Returns a new class instance
     */
    public function cloneWith(array $values): self
    {
        return self::fromArray(array_merge((array) $this, $values));
    }

}
