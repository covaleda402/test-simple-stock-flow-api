<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\ValueObject\Money;
use App\Domain\ValueObject\Quantity;
use InvalidArgumentException;

final class Product
{
    private string $id;
    private string $name;
    private Money $price;
    private Quantity $stock;

    public function __construct(string $id, string $name, Money $price, Quantity $stock)
    {
        if (trim($name) === '') {
            throw new InvalidArgumentException("El nombre del producto no puede estar vacío.");
        }

        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
        $this->stock = $stock;
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

    public function stock(): Quantity
    {
        return $this->stock;
    }

    public function addStock(Quantity $quantity): void
    {
        $this->stock = $this->stock->add($quantity);
    }

    public function removeStock(Quantity $quantity): void
    {
        // La validación de stock negativo ya está encapsulada dentro de Quantity
        $this->stock = $this->stock->subtract($quantity);
    }
}