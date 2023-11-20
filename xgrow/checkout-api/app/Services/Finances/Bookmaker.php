<?php


namespace App\Services\Finances;

use App\Exceptions\ValueMismatchException;
use App\Services\Finances\Objects\Coin;
use App\Services\Mundipagg\Calculator\Contracts\CalculatorStrategy;
use App\Services\Mundipagg\Calculator\Objects\DistributionResult;
use RuntimeException;

/**
 * Class Bookmaker
 * @package App\Services\Finances
 *
 * This class is responsible to receive the odds (charges) and paying off bets (revenue shares)
 * It needs a Bookkeeper to maintain it organized :-)
 */
class Bookmaker
{
    private Bookkeeper $bookkeeper;

    private CalculatorStrategy $calculator;

    private array $charges = [];

    private ?array $distribution = null;

    private array $distributed = [];

    private int $installmentNumber = 1;

    private ?int $noLimitInstallments = null;

    public function __construct(Bookkeeper $bookkeeper, CalculatorStrategy $calculator)
    {
        $this->bookkeeper = $bookkeeper;
        $this->calculator = $calculator;
    }

    /**
     * Add charges
     *
     * @param $chargeId
     * @param  int  $value  Value (in "centavos")
     */
    public function addCharge($chargeId, int $value)
    {
        $this->charges[$chargeId] = $value;
    }

    public function sumCharges(): int
    {
        $sum = 0;
        foreach ($this->charges as $value) {
            $sum += $value;
        }
        return $sum;
    }

    public function setNoLimitInstallment(int $currentInstallment, int $noLimitInstallments): self
    {
        $this->installmentNumber = $currentInstallment;
        $this->noLimitInstallments = $noLimitInstallments;
        return $this;
    }

    /**
     * Distribute all the money for all charges and all revenues (Plan + Order Bumps)
     */
    public function distribute(): self
    {
        $totalValue = $this->bookkeeper->getTotalPrice();
        $totalCoupon = $this->bookkeeper->getTotalCoupon();

        $hasNoLimitInstallments = !is_null($this->noLimitInstallments) && $this->noLimitInstallments > 1;

        if ($hasNoLimitInstallments) {
            $totalValue = Coin::fromInt($totalValue)
                ->installmentNumber($this->installmentNumber, $this->noLimitInstallments)
                ->getAmount();

            $totalCoupon = Coin::fromInt($totalCoupon)
                ->installmentNumber($this->installmentNumber, $this->noLimitInstallments)
                ->getAmount();
        }

        $totalValueWithDiscounts = $totalValue - $totalCoupon;

        $totalCharge = $this->sumCharges();
        if ($totalCharge < $totalValueWithDiscounts) {
            throw new ValueMismatchException("Can't distribute, Total charge is lower than expected (expected {$totalValueWithDiscounts}, received {$totalCharge})");
        }

        $this->initializeDistribution();

        foreach ($this->bookkeeper->getAllValues() as $planId => $orderValues) {

            $valueWithDiscounts = $orderValues->getPrice() - $orderValues->getCoupon();

            if ($hasNoLimitInstallments) {
                $valueWithDiscounts = Coin::fromInt($valueWithDiscounts)
                    ->installmentNumber($this->installmentNumber, $this->noLimitInstallments)
                    ->getAmount();
            }

            $priceRatio = $valueWithDiscounts / $totalValueWithDiscounts;

            $amount = $this->calculator->calculate(
                $orderValues->getPrice(),
                $orderValues->getCoupon(),
                $orderValues->getPercentSplit(),
                $orderValues->getClientTaxTransaction(),
                $this->installmentNumber,
                $this->noLimitInstallments ?? 1
            );

            $numberOfCharges = count($this->charges ?? []);
            $index = 0;
            $accumulatedTax = 0;
            foreach ($this->charges as $chargeId => $chargeValue) {
                $chargeRatio = $chargeValue / $totalCharge;

                $value = (int) ($amount->getValue() * $chargeRatio);
                $chargeValueWithDiscounts = (int) ($valueWithDiscounts * $chargeRatio);

                if ($index < $numberOfCharges - 1) {
                    $tax = (int) round($amount->getTax() * $chargeRatio);
                } else {
                    $tax = $amount->getTax() - $accumulatedTax; // remaining tax
                }

                $accumulatedTax += $tax;

                $customerValue = $chargeValueWithDiscounts - $tax;

                $coupon = (int) ($amount->getCoupon() * $chargeRatio);

                $valueWithInterests = (int) ($totalCharge * $priceRatio * $chargeRatio);

                $this->distribution[$planId][$chargeId] = new DistributionResult(
                    $value,
                    $chargeValueWithDiscounts,
                    $valueWithInterests,
                    $customerValue,
                    $tax,
                    $coupon,
                );

                $this->accumulate($value, $chargeValueWithDiscounts, $valueWithInterests, $customerValue, $tax, $coupon);

                $index++;
            }
        }

        $distributed = $this->distributed;

        $mainPlanId = $this->bookkeeper->getMainId();
        $firstChargeId = array_key_first($this->distribution[$mainPlanId]);

        /** @var \App\Services\Mundipagg\Calculator\Objects\DistributionResult $original */
        $original = $this->distribution[$mainPlanId][$firstChargeId];

        $remainder = new DistributionResult(
            $totalValue - $distributed['value'],
            $totalValueWithDiscounts - $distributed['valueWithDiscounts'],
            $totalCharge - $distributed['valueWithInterests'],
            $totalValueWithDiscounts - $distributed['customerValue'] - $distributed['tax'],
            0,
            $totalCoupon - $distributed['coupon'],
        );

        // add remainders to first payment of main product
        $this->distribution[$mainPlanId][$firstChargeId] = $original->join($remainder);

        return $this;
    }

    private function initializeDistribution()
    {
        $this->distribution = null;
        $this->distributed = [
            'value' => 0,
            'valueWithDiscounts' => 0,
            'valueWithInterests' => 0,
            'customerValue' => 0,
            'tax' => 0,
            'coupon' => 0,
        ];

        return $this->distributed;
    }

    private function accumulate(
        int $value,
        int $valueWithDiscounts,
        int $valueWithInterests,
        int $customerValue,
        int $tax,
        int $coupon
    ) {
        $this->distributed['value'] += $value;
        $this->distributed['valueWithDiscounts'] += $valueWithDiscounts;
        $this->distributed['valueWithInterests'] += $valueWithInterests;
        $this->distributed['customerValue'] += $customerValue;
        $this->distributed['tax'] += $tax;
        $this->distributed['coupon'] += $coupon;

        return $this->distributed;
    }

    public function getDistribution($planId, $chargeId): DistributionResult
    {
        if (is_null($this->distribution)) {
            throw new RuntimeException('Please distribute first');
        }

        $result = $this->distribution[$planId][$chargeId] ?? null;

        if (is_null($result)) {
            throw new RuntimeException('Plan ID or Charge ID not defined');
        }

        return $result;
    }

}
