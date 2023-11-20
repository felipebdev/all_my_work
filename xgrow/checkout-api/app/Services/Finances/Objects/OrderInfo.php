<?php

namespace App\Services\Finances\Objects;

use App\OneClick;
use App\Plan;
use App\Services\Finances\Payment\Exceptions\InvalidOrderException;
use App\Services\Finances\Product\ProductAmountService;
use App\Services\Objects\FillableObject;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * Immutability must be enforced through protected setters/attributes and public getters.
 *
 * @package App\Services\Finances\Objects
 */
class OrderInfo extends FillableObject
{
    public static function fromRequestData(array $data): self
    {
        $orderInfo = (new self())
            ->withPlatformId($data['platform_id'])
            ->withPaymentMethod($data['payment_method'])
            ->withPlanId($data['plan_id'])
            ->withAffiliateId($data['affiliate_id'] ?? null)
            ->withCoupom($data['cupom'] ?? '')
            ->withSubscriberId($data['subscriber_id'])
            ->withCcInfo($data['cc_info'] ?? [])
            ->withPayments($data['payments'] ?? []);

        $orderInfo->withAddressInfo(AddressInfo::fromRequestData($data));

        $orderInfo->withOrderBumpsBag(OrderBumpsBag::fromRequestData($data));

        return $orderInfo;
    }

    public static function fromOneClick(string $planId, OneClick $oneClick): self
    {
        $plan = Plan::findOrFail($planId);

        $orderInfo = (new self())
            ->withPlatformId($plan->platform->id)
            ->withPlanId($plan->id)
            ->withSubscriberId($oneClick->subscriber_id)
            ->withAffiliateId($oneClick->affiliate_id ?? null) // @todo add this column to the database
            ->withPaymentMethod($oneClick->payment_method)
            ->withCcInfo([
                [
                    'value' => $plan->getPrice(),
                    'card_id' => $oneClick->card_id ?? null,
                    'installment' => $oneClick->installments,
                ]
            ]);

        $orderInfo->withOrderBumpsBag(OrderBumpsBag::empty());

        return $orderInfo;
    }

    protected string $platformId;
    protected int $planId;
    protected int $subscriberId;
    protected string $paymentMethod = '';
    protected string $coupom = '';
    protected array $ccInfo = [];
    protected AddressInfo $addressInfo;
    protected OrderBumpsBag $orderBumpsBag;
    protected ?string $affiliateId = null;
    protected array $payments = [];

    protected int $installmentSelected;

    protected bool $isUpsell = false;

    public OrderInfoModelFinder $finder;

    public OrderInfoPriceTag $priceTag;

    /**
     * This class must not be instantiated directly, use static factory instead.
     */
    protected function __construct(?array $data = [])
    {
        parent::__construct($data);
        $this->finder = new OrderInfoModelFinder($this);
        $this->priceTag = new OrderInfoPriceTag($this);
    }

    public function getPlatformId(): string
    {
        return $this->platformId;
    }

    protected function withPlatformId(string $platformId): self
    {
        $this->platformId = $platformId;
        return $this;
    }

    public function getPlanId(): int
    {
        return $this->planId;
    }

    protected function withPlanId(int $planId): self
    {
        $this->planId = $planId;
        return $this;
    }

    public function getSubscriberId(): int
    {
        return $this->subscriberId;
    }

    protected function withSubscriberId(int $subscriberId): self
    {
        $this->subscriberId = $subscriberId;
        return $this;
    }

    public function getPaymentMethod(): string
    {
        return $this->paymentMethod;
    }

    protected function withPaymentMethod(string $paymentMethod): self
    {
        $this->paymentMethod = $paymentMethod;
        return $this;
    }

    public function getCoupom(): string
    {
        return $this->coupom ?? '';
    }

    protected function withCoupom(?string $coupom): self
    {
        $this->coupom = $coupom ?? '';
        return $this;
    }

    public function getCcInfo(): array
    {
        return $this->ccInfo;
    }

    protected function withCcInfo(array $ccInfo): self
    {
        $this->ccInfo = $ccInfo;
        return $this;
    }

    public function getPayments(): Collection
    {
        return collect($this->payments ?? []);
    }

    protected function withPayments(array $payments): self
    {
        $this->payments = $payments;
        return $this;
    }

    /**
     * @return $this
     * @throws \App\Services\Finances\Payment\Exceptions\InvalidOrderException
     */
    public function validateCcInfo(): self
    {
        if (empty($this->getCcInfo())) {
            throw new InvalidOrderException('Dados dos cartões inválidos');
        }
        return $this;
    }

    public function validatePayments(): self
    {
        if (empty($this->payments)) {
            throw new InvalidOrderException('Dados dos pagamentos inválidos');
        }
        return $this;
    }

    public function getAddressInfo(): AddressInfo
    {
        return $this->addressInfo;
    }

    protected function withAddressInfo(AddressInfo $addressInfo): self
    {
        $this->addressInfo = $addressInfo;
        return $this;
    }

    public function getInstallmentSelected(): int
    {
        return $this->installmentSelected ?? 0;
    }

    /**
     * @deprecated
     */
    public function setInstallmentSelected(int $installmentSelected): self
    {
        $this->installmentSelected = $installmentSelected;
        return $this;
    }

    public function isUpsell(): bool
    {
        return $this->isUpsell;
    }

    public function setIsUpsell(bool $isUpsell = true): self
    {
        $this->isUpsell = $isUpsell;
        return $this;
    }

    public function getOrderBumpsBag(): OrderBumpsBag
    {
        return $this->orderBumpsBag;
    }

    protected function withOrderBumpsBag(OrderBumpsBag $orderBumpsBag): self
    {
        $this->orderBumpsBag = $orderBumpsBag;
        return $this;
    }

    public function withAffiliateId(?string $affiliateId): self
    {
        Log::withContext(['with_affiliate_id' => $affiliateId]);
        $this->affiliateId = $affiliateId;
        return $this;
    }

    public function getAffiliateId(): ?string
    {
        return $this->affiliateId;
    }

    /**
     * Get all plan IDs (Main product + Order Bumps)
     *
     * @return array
     */
    public function getAllPlanIds(): array
    {
        return array_merge([$this->getPlanId()], $this->getOrderBumpsBag()->getOrderBumpsIds());
    }

}
