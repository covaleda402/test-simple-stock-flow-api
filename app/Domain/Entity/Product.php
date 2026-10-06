<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Exception\BusinessRuleValidationException;
use App\Domain\ValueObject\Money;
use App\Domain\ValueObject\Quantity;

final class Product
{
    private string $id;
    private string $name;
    private Money $price;
    private int $stock;
    private string $categoryId;
    private ?string $imageKey;

    public function __construct(
        string $id,
        string $name,
        Money $price,
        int $stock,
        string $categoryId,
        ?string $imageKey = null
    ) {
        $trimmedName = trim($name);
        if ($trimmedName === '') {
            throw new BusinessRuleValidationException("El nombre del producto es obligatorio.");
        }

        if (!$price->isPositive()) {
            throw new BusinessRuleValidationException("El precio debe ser mayor a cero.");
        }

        if ($stock < 0) {
            throw new BusinessRuleValidationException("El stock inicial no puede ser negativo.");
        }

        $trimmedCategory = trim($categoryId);
        if ($trimmedCategory === '' || $trimmedCategory === '00000000-0000-0000-0000-000000000000') {
            throw new BusinessRuleValidationException("La categoría es obligatoria.");
        }

        $this->id = $id;
        $this->name = $trimmedName;
        $this->price = $price;
        $this->stock = $stock;
        $this->categoryId = $trimmedCategory;
        $this->imageKey = ($imageKey !== null && trim($imageKey) !== '') ? trim($imageKey) : null;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function price(): Money
    {
        return $this->price;
    }

    public function stock(): int
    {
        return $this->stock;
    }

    public function categoryId(): string
    {
        return $this->categoryId;
    }

    public function imageKey(): ?string
    {
        return $this->imageKey;
    }

    public function rename(string $newName): void
    {
        $trimmed = trim($newName);
        if ($trimmed === '') {
            throw new BusinessRuleValidationException("El nombre del producto es obligatorio.");
        }
        $this->name = $trimmed;
    }

    public function changePrice(Money $newPrice): void
    {
        if (!$newPrice->isPositive()) {
            throw new BusinessRuleValidationException("El precio debe ser mayor a cero.");
        }
        $this->price = $newPrice;
    }

    public function setCategory(string $categoryId): void
    {
        $trimmed = trim($categoryId);
        if ($trimmed === '' || $trimmed === '00000000-0000-0000-0000-000000000000') {
            throw new BusinessRuleValidationException("La categoría es obligatoria.");
        }
        $this->categoryId = $trimmed;
    }

    public function attachImage(?string $imageKey): void
    {
        $this->imageKey = ($imageKey !== null && trim($imageKey) !== '') ? trim($imageKey) : null;
    }

    public function withdraw(Quantity $quantity): void
    {
        if ($this->stock < $quantity->value()) {
            throw new BusinessRuleValidationException(
                "Stock insuficiente para '{$this->name}': disponible {$this->stock}, solicitado {$quantity->value()}."
            );
        }

        $this->stock -= $quantity->value();
    }

    public function restock(int $amount): void
    {
        if ($amount < 0) {
            throw new BusinessRuleValidationException("La cantidad a reponer no puede ser negativa.");
        }

        $this->stock += $amount;
    }
}