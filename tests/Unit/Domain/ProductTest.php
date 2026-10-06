<?php

declare(strict_types=1);

namespace Tests\Unit\Domain;

use App\Domain\Entity\Product;
use App\Domain\Exception\BusinessRuleValidationException;
use App\Domain\ValueObject\Money;
use App\Domain\ValueObject\Quantity;
use PHPUnit\Framework\TestCase;

final class ProductTest extends TestCase
{
    public function test_creates_product_successfully(): void
    {
        $product = new Product(
            'prod-1',
            'Martillo',
            new Money('25000.00'),
            10,
            'cat-herramientas'
        );

        $this->assertSame('prod-1', $product->id());
        $this->assertSame('Martillo', $product->name());
        $this->assertSame('25000.00', (string) $product->price()->amount());
        $this->assertSame(10, $product->stock());
        $this->assertSame('cat-herramientas', $product->categoryId());
    }

    public function test_rejects_empty_name(): void
    {
        $this->expectException(BusinessRuleValidationException::class);
        $this->expectExceptionMessage("El nombre del producto es obligatorio.");

        new Product(
            'prod-1',
            '   ',
            new Money('10000.00'),
            5,
            'cat-1'
        );
    }

    public function test_rejects_zero_or_negative_price(): void
    {
        $this->expectException(BusinessRuleValidationException::class);
        $this->expectExceptionMessage("El precio debe ser mayor a cero.");

        new Product(
            'prod-1',
            'Clavo',
            Money::zero(),
            5,
            'cat-1'
        );
    }

    public function test_rejects_negative_stock(): void
    {
        $this->expectException(BusinessRuleValidationException::class);
        $this->expectExceptionMessage("El stock inicial no puede ser negativo.");

        new Product(
            'prod-1',
            'Tornillo',
            new Money('500.00'),
            -1,
            'cat-1'
        );
    }

    public function test_withdraws_stock_successfully(): void
    {
        $product = new Product(
            'prod-1',
            'Taladro',
            new Money('150000.00'),
            5,
            'cat-1'
        );

        $product->withdraw(new Quantity(2));
        $this->assertSame(3, $product->stock());
    }

    public function test_rejects_withdraw_exceeding_stock(): void
    {
        $product = new Product(
            'prod-1',
            'Taladro',
            new Money('150000.00'),
            3,
            'cat-1'
        );

        $this->expectException(BusinessRuleValidationException::class);
        $this->expectExceptionMessage("Stock insuficiente para 'Taladro': disponible 3, solicitado 5.");

        $product->withdraw(new Quantity(5));
    }
}
