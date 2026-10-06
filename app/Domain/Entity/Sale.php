<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Exception\BusinessRuleValidationException;
use App\Domain\ValueObject\Money;
use App\Domain\ValueObject\Quantity;
use DateTimeImmutable;
use DateTimeZone;

final class Sale
{
    private string $id;
    private DateTimeImmutable $soldAt;
    private string $soldByUsername;
    private string $soldByUserId;
    /** @var array<string, SaleItem> key is product_id */
    private array $items = [];

    /**
     * @param string $id
     * @param string $soldByUsername
     * @param string $soldByUserId
     * @param DateTimeImmutable|null $soldAt
     * @param array<int, SaleItem> $existingItems
     */
    public function __construct(
        string $id,
        string $soldByUsername,
        string $soldByUserId,
        ?DateTimeImmutable $soldAt = null,
        array $existingItems = []
    ) {
        $trimmedUsername = trim($soldByUsername);
        if ($trimmedUsername === '') {
            throw new BusinessRuleValidationException("El usuario vendedor es obligatorio.");
        }

        $trimmedUserId = trim($soldByUserId);
        if ($trimmedUserId === '') {
            throw new BusinessRuleValidationException("El ID del vendedor es obligatorio.");
        }

        $this->id = $id;
        $this->soldByUsername = $trimmedUsername;
        $this->soldByUserId = $trimmedUserId;
        $this->soldAt = ($soldAt ?? new DateTimeImmutable('now', new DateTimeZone('UTC')))->setTimezone(new DateTimeZone('UTC'));

        foreach ($existingItems as $item) {
            $this->items[$item->productId()] = $item;
        }
    }

    public function id(): string
    {
        return $this->id;
    }

    public function soldAt(): DateTimeImmutable
    {
        return $this->soldAt;
    }

    public function soldByUsername(): string
    {
        return $this->soldByUsername;
    }

    public function soldByUserId(): string
    {
        return $this->soldByUserId;
    }

    /**
     * @return array<int, SaleItem>
     */
    public function items(): array
    {
        return array_values($this->items);
    }

    /**
     * Añade un renglón a la venta y descuenta stock del agregado Product (RN-01, RN-05, RN-06).
     */
    public function addItem(
        string $itemId,
        Product $product,
        string $categoryName,
        Quantity $quantity
    ): SaleItem {
        if (isset($this->items[$product->id()])) {
            throw new BusinessRuleValidationException("La venta tiene productos repetidos.");
        }

        // Descuenta el stock del producto según la regla de negocio
        $product->withdraw($quantity);

        $item = new SaleItem(
            $itemId,
            $this->id,
            $product->id(),
            $product->name(),
            $categoryName,
            $quantity,
            $product->price()
        );

        $this->items[$product->id()] = $item;

        return $item;
    }

    /**
     * Valida que la venta contenga al menos una línea (RN-04).
     */
    public function ensureConfirmable(): void
    {
        if (empty($this->items)) {
            throw new BusinessRuleValidationException("La venta debe tener al menos un ítem.");
        }
    }

    /**
     * Total calculado en memoria sumando las líneas (Artículo VII de la Constitución, RN-12).
     */
    public function total(): Money
    {
        $sum = Money::zero();
        foreach ($this->items as $item) {
            $sum = $sum->plus($item->subtotal());
        }

        return $sum;
    }
}
