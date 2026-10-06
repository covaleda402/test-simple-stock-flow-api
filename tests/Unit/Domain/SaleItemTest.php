<?php

declare(strict_types=1);

namespace Tests\Unit\Domain;

use App\Domain\Entity\SaleItem;
use App\Domain\ValueObject\Money;
use App\Domain\ValueObject\Quantity;
use PHPUnit\Framework\TestCase;

final class SaleItemTest extends TestCase
{
    public function test_freezes_data_and_calculates_subtotal_in_memory(): void
    {
        $item = new SaleItem(
            'item-1',
            'sale-1',
            'prod-1',
            'Cinta Aislante',
            'Electricidad',
            new Quantity(4),
            new Money('3500.00')
        );

        $this->assertSame('Cinta Aislante', $item->productName());
        $this->assertSame('Electricidad', $item->categoryName());
        $this->assertSame(4, $item->quantity()->value());
        $this->assertSame('3500.00', (string) $item->unitPrice()->amount());

        // 4 * 3500 = 14000
        $this->assertSame('14000.00', (string) $item->subtotal()->amount());
    }
}
