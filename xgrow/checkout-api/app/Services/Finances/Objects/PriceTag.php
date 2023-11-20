<?php


namespace App\Services\Finances\Objects;

/**
 * Class PriceTag stores simple information about a product/plan
 *
 * @package App\Services\Finances\Objects
 */
#[Immutable]
class PriceTag
{
    public static function fromDecimal(string $id, float $decimalPrice, ?string $description = ''): self
    {
        $description ??= '';
        return new self($id, round($decimalPrice * 100, 0, PHP_ROUND_HALF_UP), $description);
    }

    public static function fromInt(string $id, int $amount, ?string $description = ''): self
    {
        $description ??= '';
        return new self($id, $amount, $description);
    }

    protected string $id;

    protected int $price;

    protected string $description = '';

    protected function __construct(string $id, int $price, string $description = '')
    {
        $this->id = $id;
        $this->price = $price;
        $this->description = $description;
    }

    #[Pure]
    public function getId(): int
    {
        return $this->id;
    }

    #[Pure]
    public function getAmount(): int
    {
        return $this->price;
    }

    #[Pure]
    public function getDescription(): string
    {
        return $this->description;
    }
}
