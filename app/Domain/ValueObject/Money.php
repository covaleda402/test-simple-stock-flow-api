<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use InvalidArgumentException;

final class Money
{
    private BigDecimal $amount;
    private string $currency;

    public function __construct(string|float $amount, string $currency = 'COP')
    {
        if ($currency !== 'COP') {
            throw new InvalidArgumentException("Solo se permite moneda COP");
        }

        // Redondeo determinista a 2 decimales (HALF_UP)
        $this->amount = BigDecimal::of($amount)->toScale(2, RoundingMode::HALF_UP);
        $this->currency = $currency;
    }

    public function amount(): BigDecimal
    {
        return $this->amount;
    }

    public function currency(): string
    {
        return $this->currency;
    }

    public function add(Money $other): self
    {
        return new self($this->amount->plus($other->amount()), $this->currency);
    }

    public function multiply(int $multiplier): self
    {
        return new self($this->amount->multipliedBy($multiplier), $this->currency);
    }

    public function equals(Money $other): bool
    {
        return $this->amount->isEqualTo($other->amount()) && $this->currency === $other->currency();
    }
}