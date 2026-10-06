<?php

declare(strict_types=1);

namespace Tests\Unit\Domain;

use App\Domain\Exception\BusinessRuleValidationException;
use App\Domain\ValueObject\Quantity;
use PHPUnit\Framework\TestCase;

final class QuantityTest extends TestCase
{
    public function test_creates_valid_quantity(): void
    {
        $qty = new Quantity(5);
        $this->assertSame(5, $qty->value());
    }

    public function test_rejects_zero_quantity(): void
    {
        $this->expectException(BusinessRuleValidationException::class);
        $this->expectExceptionMessage("La cantidad debe ser mayor a cero.");

        new Quantity(0);
    }

    public function test_rejects_negative_quantity(): void
    {
        $this->expectException(BusinessRuleValidationException::class);
        $this->expectExceptionMessage("La cantidad debe ser mayor a cero.");

        new Quantity(-2);
    }

    public function test_adds_quantities(): void
    {
        $q1 = new Quantity(3);
        $q2 = new Quantity(7);

        $result = $q1->add($q2);
        $this->assertSame(10, $result->value());
    }
}
