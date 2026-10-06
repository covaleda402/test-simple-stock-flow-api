<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use App\Domain\Exception\BusinessRuleValidationException;

final class Quantity
{
    private int $value;

    /**
     * @param int $value
     * @throws BusinessRuleValidationException
     */
    public function __construct(int $value)
    {
        if ($value <= 0) {
            throw new BusinessRuleValidationException("La cantidad debe ser mayor a cero.");
        }

        $this->value = $value;
    }

    public static function of(int $value): self
    {
        return new self($value);
    }

    public function value(): int
    {
        return $this->value;
    }

    public function add(Quantity $other): self
    {
        return new self($this->value + $other->value());
    }

    public function equals(Quantity $other): bool
    {
        return $this->value === $other->value();
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}