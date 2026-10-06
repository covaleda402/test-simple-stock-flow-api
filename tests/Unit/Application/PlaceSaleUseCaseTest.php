<?php

declare(strict_types=1);

namespace Tests\Unit\Application;

use App\Application\Ports\Outbound\CategoryRepositoryInterface;
use App\Application\Ports\Outbound\ProductRepositoryInterface;
use App\Application\Ports\Outbound\SaleRepositoryInterface;
use App\Application\Ports\Outbound\TransactionManagerInterface;
use App\Application\UseCase\PlaceSaleUseCase;
use App\Domain\Entity\Category;
use App\Domain\Entity\Product;
use App\Domain\Entity\Sale;
use App\Domain\Exception\BusinessRuleValidationException;
use App\Domain\ValueObject\Money;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class PlaceSaleUseCaseTest extends TestCase
{
    public function test_places_sale_and_deducts_stock_atomically(): void
    {
        $product = new Product('prod-1', 'Cable', new Money('2000.00'), 10, 'cat-1');

        $prodRepo = new class($product) implements ProductRepositoryInterface {
            public function __construct(private Product $product) {}
            public function findById(string $id): ?Product { return $this->product; }
            public function findActiveById(string $id): ?Product { return $this->product; }
            public function save(Product $product): void { $this->product = $product; }
            public function delete(string $id): void {}
            public function search(?string $search, ?string $categoryId, int $page, int $size): array { return []; }
        };

        $catRepo = new class implements CategoryRepositoryInterface {
            public function findAll(): array { return []; }
            public function findById(string $id): ?Category { return new Category('cat-1', 'Electricidad'); }
            public function exists(string $id): bool { return true; }
        };

        $saleRepo = new class implements SaleRepositoryInterface {
            public ?Sale $saved = null;
            public function save(Sale $sale): void { $this->saved = $sale; }
            public function findById(string $id): ?Sale { return $this->saved; }
            public function findBetweenDates(DateTimeImmutable $from, DateTimeImmutable $to, int $page, int $size): array { return []; }
            public function getSalesReport(DateTimeImmutable $from, DateTimeImmutable $to): array { return []; }
        };

        $txManager = new class implements TransactionManagerInterface {
            public function transactional(callable $operation): mixed { return $operation(); }
        };

        $useCase = new PlaceSaleUseCase($saleRepo, $prodRepo, $catRepo, $txManager);

        $saleId = $useCase->execute(
            [['productId' => 'prod-1', 'quantity' => 3]],
            'vendedor1',
            'user-1'
        );

        $this->assertNotEmpty($saleId);
        $this->assertSame(7, $product->stock());
        $this->assertNotNull($saleRepo->saved);
    }

    public function test_rejects_sale_when_stock_insufficient(): void
    {
        $product = new Product('prod-1', 'Cable', new Money('2000.00'), 2, 'cat-1');

        $prodRepo = new class($product) implements ProductRepositoryInterface {
            public function __construct(private Product $product) {}
            public function findById(string $id): ?Product { return $this->product; }
            public function findActiveById(string $id): ?Product { return $this->product; }
            public function save(Product $product): void {}
            public function delete(string $id): void {}
            public function search(?string $search, ?string $categoryId, int $page, int $size): array { return []; }
        };

        $catRepo = new class implements CategoryRepositoryInterface {
            public function findAll(): array { return []; }
            public function findById(string $id): ?Category { return new Category('cat-1', 'Electricidad'); }
            public function exists(string $id): bool { return true; }
        };

        $saleRepo = new class implements SaleRepositoryInterface {
            public function save(Sale $sale): void {}
            public function findById(string $id): ?Sale { return null; }
            public function findBetweenDates(DateTimeImmutable $from, DateTimeImmutable $to, int $page, int $size): array { return []; }
            public function getSalesReport(DateTimeImmutable $from, DateTimeImmutable $to): array { return []; }
        };

        $txManager = new class implements TransactionManagerInterface {
            public function transactional(callable $operation): mixed { return $operation(); }
        };

        $useCase = new PlaceSaleUseCase($saleRepo, $prodRepo, $catRepo, $txManager);

        $this->expectException(BusinessRuleValidationException::class);
        $this->expectExceptionMessage("Stock insuficiente para 'Cable': disponible 2, solicitado 5.");

        $useCase->execute(
            [['productId' => 'prod-1', 'quantity' => 5]],
            'vendedor1',
            'user-1'
        );
    }
}
