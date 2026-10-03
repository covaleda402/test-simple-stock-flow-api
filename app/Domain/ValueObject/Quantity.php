<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use InvalidArgumentException;

final class Quantity
{
    private int $value;

    public function __construct(int $value)
    {
        if ($value < 0) {
            throw new InvalidArgumentException("La cantidad de stock no puede ser negativa.");
        }

        $this->value = $value;
    }

    public function value(): int
    {
        return $this->value;
    }

    public function add(Quantity $other): self
    {
        return new self($this->value + $other->value());
    }

    public function subtract(Quantity $other): self
    {
        $newValue = $this->value - $other->value();
        
        if ($newValue < 0) {
            throw new InvalidArgumentException("Stock insuficiente para realizar la sustracción.");
        }

        return new self($newValue);
    }

    public function equals(Quantity $other): bool
    {
        return $this->value === $other->value();
    }
}