<?php

declare(strict_types=1);

namespace Tests\Unit\Domain;

use App\Domain\Exception\BusinessRuleValidationException;
use App\Domain\ValueObject\Money;
use PHPUnit\Framework\TestCase;

final class MoneyTest extends TestCase
{
    public function test_creates_money_with_cop_currency_and_two_decimals(): void
    {
        $money = new Money('100.50', 'COP');

        $this->assertSame('100.50', (string) $money->amount());
        $this->assertSame('COP', $money->currency());
    }

    public function test_rounds_half_up_deterministically(): void
    {
        $money = new Money('10.005');
        $this->assertSame('10.01', (string) $money->amount());

        $money2 = new Money('10.004');
        $this->assertSame('10.00', (string) $money2->amount());
    }

    public function test_rejects_negative_amount(): void
    {
        $this->expectException(BusinessRuleValidationException::class);
        $this->expectExceptionMessage("El importe no puede ser negativo.");

        new Money('-5.00');
    }

    public function test_rejects_non_cop_currency(): void
    {
        $this->expectException(BusinessRuleValidationException::class);
        $this->expectExceptionMessage("Solo se permite moneda COP.");

        new Money('100.00', 'USD');
    }

    public function test_adds_two_money_instances(): void
    {
        $m1 = new Money('15.50');
        $m2 = new Money('10.25');

        $result = $m1->plus($m2);

        $this->assertSame('25.75', (string) $result->amount());
    }

    public function test_multiplies_by_integer(): void
    {
        $money = new Money('12.50');
        $result = $money->multiply(3);

        $this->assertSame('37.50', (string) $result->amount());
    }
}
