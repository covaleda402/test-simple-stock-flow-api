<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use App\Domain\Exception\BusinessRuleValidationException;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;

final class Money
{
    private BigDecimal $amount;
    private string $currency;

    public function __construct(string|float|int|BigDecimal $amount, string $currency = 'COP')
    {
        if ($currency !== 'COP') {
            throw new BusinessRuleValidationException("Solo se permite moneda COP.");
        }

        // Redondeo determinista a 2 decimales (HALF_UP)
        $parsed = BigDecimal::of($amount)->toScale(2, RoundingMode::HalfUp);
        if ($parsed->isNegative()) {
            throw new BusinessRuleValidationException("El importe no puede ser negativo.");
        }

        $this->amount = $parsed;
        $this->currency = $currency;
    }

    public static function zero(string $currency = 'COP'): self
    {
        return new self('0.00', $currency);
    }

    public function isPositive(): bool
    {
        return $this->amount->isPositive();
    }

    public function isZero(): bool
    {
        return $this->amount->isZero();
    }

    public function amount(): BigDecimal
    {
        return $this->amount;
    }

    public function currency(): string
    {
        return $this->currency;
    }

    public function plus(Money $other): self
    {
        return new self($this->amount->plus($other->amount()), $this->currency);
    }

    public function add(Money $other): self
    {
        return $this->plus($other);
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