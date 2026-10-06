<?php

declare(strict_types=1);

namespace Tests\Unit\Domain;

use App\Domain\Entity\Product;
use App\Domain\Entity\Sale;
use App\Domain\Exception\BusinessRuleValidationException;
use App\Domain\ValueObject\Money;
use App\Domain\ValueObject\Quantity;
use PHPUnit\Framework\TestCase;

final class SaleTest extends TestCase
{
    public function test_adds_item_and_calculates_total_in_memory(): void
    {
        $sale = new Sale('sale-1', 'cajero1', 'user-1');

        $p1 = new Product('prod-1', 'Pintura Blanca', new Money('30000.00'), 10, 'cat-pinturas');
        $p2 = new Product('prod-2', 'Brocha', new Money('5000.00'), 20, 'cat-herramientas');

        $sale->addItem('item-1', $p1, 'Pinturas', new Quantity(2));
        $sale->addItem('item-2', $p2, 'Herramientas', new Quantity(3));

        $this->assertCount(2, $sale->items());
        // 2 * 30000 = 60000 + 3 * 5000 = 15000 -> 75000
        $this->assertSame('75000.00', (string) $sale->total()->amount());

        // Verifica que el stock del producto se haya descontado en el agregado
        $this->assertSame(8, $p1->stock());
        $this->assertSame(17, $p2->stock());
    }

    public function test_rejects_empty_sale_confirmation(): void
    {
        $sale = new Sale('sale-1', 'cajero1', 'user-1');

        $this->expectException(BusinessRuleValidationException::class);
        $this->expectExceptionMessage("La venta debe tener al menos un ítem.");

        $sale->ensureConfirmable();
    }

    public function test_rejects_repeated_product_in_same_sale(): void
    {
        $sale = new Sale('sale-1', 'cajero1', 'user-1');
        $product = new Product('prod-1', 'Pintura', new Money('30000.00'), 10, 'cat-1');

        $sale->addItem('item-1', $product, 'Pinturas', new Quantity(1));

        $this->expectException(BusinessRuleValidationException::class);
        $this->expectExceptionMessage("La venta tiene productos repetidos.");

        $sale->addItem('item-2', $product, 'Pinturas', new Quantity(2));
    }
}
