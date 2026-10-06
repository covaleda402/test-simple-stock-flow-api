<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Exception\BusinessRuleValidationException;
use App\Domain\ValueObject\Money;
use App\Domain\ValueObject\Quantity;

final class SaleItem
{
    private string $id;
    private string $saleId;
    private string $productId;
    private string $productName;
    private string $categoryName;
    private Quantity $quantity;
    private Money $unitPrice;

    public function __construct(
        string $id,
        string $saleId,
        string $productId,
        string $productName,
        string $categoryName,
        Quantity $quantity,
        Money $unitPrice
    ) {
        $trimmedProductName = trim($productName);
        if ($trimmedProductName === '') {
            throw new BusinessRuleValidationException("El nombre del producto en el ítem es obligatorio.");
        }

        $trimmedCategoryName = trim($categoryName);
        if ($trimmedCategoryName === '') {
            throw new BusinessRuleValidationException("El nombre de la categoría en el ítem es obligatorio.");
        }

        if (!$unitPrice->isPositive()) {
            throw new BusinessRuleValidationException("El precio unitario debe ser mayor a cero.");
        }

        $this->id = $id;
        $this->saleId = $saleId;
        $this->productId = $productId;
        $this->productName = $trimmedProductName;
        $this->categoryName = $trimmedCategoryName;
        $this->quantity = $quantity;
        $this->unitPrice = $unitPrice;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function saleId(): string
    {
        return $this->saleId;
    }

    public function productId(): string
    {
        return $this->productId;
    }

    public function productName(): string
    {
        return $this->productName;
    }

    public function categoryName(): string
    {
        return $this->categoryName;
    }

    public function quantity(): Quantity
    {
        return $this->quantity;
    }

    public function unitPrice(): Money
    {
        return $this->unitPrice;
    }

    /**
     * Subtotal derivado calculado en memoria (Artículo VII de la Constitución).
     */
    public function subtotal(): Money
    {
        return $this->unitPrice->multiply($this->quantity->value());
    }
}
