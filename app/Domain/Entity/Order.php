<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\ValueObject\Money;
use App\Domain\ValueObject\Quantity;
use InvalidArgumentException;
use DateTimeImmutable;

final class Order
{
    private string $id;
    private string $productId;
    private Quantity $quantity;
    private Money $totalPrice;
    private DateTimeImmutable $createdAt;
    private string $status;

    public function __construct(
        string $id,
        string $productId,
        Quantity $quantity,
        Money $unitPrice,
        ?DateTimeImmutable $createdAt = null
    ) {
        if ($quantity->value() === 0) {
            throw new InvalidArgumentException("La orden debe tener al menos un producto.");
        }

        $this->id = $id;
        $this->productId = $productId;
        $this->quantity = $quantity;
        // El precio total es el precio unitario multiplicado por la cantidad
        $this->totalPrice = $unitPrice->multiply($quantity->value());
        $this->createdAt = $createdAt ?? new DateTimeImmutable();
        $this->status = 'COMPLETED'; // Estado por defecto según negocio
    }

    public function id(): string
    {
        return $this->id;
    }

    public function productId(): string
    {
        return $this->productId;
    }

    public function quantity(): Quantity
    {
        return $this->quantity;
    }

    public function totalPrice(): Money
    {
        return $this->totalPrice;
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
    
    public function status(): string
    {
        return $this->status;
    }
}