<?php

declare(strict_types=1);

namespace Tests\Unit\Application;

use App\Application\Ports\Outbound\CategoryRepositoryInterface;
use App\Application\Ports\Outbound\ProductRepositoryInterface;
use App\Application\UseCase\CreateProductUseCase;
use App\Domain\Entity\Category;
use App\Domain\Entity\Product;
use App\Domain\Exception\BusinessRuleValidationException;
use PHPUnit\Framework\TestCase;

final class CreateProductUseCaseTest extends TestCase
{
    public function test_creates_product_when_category_exists(): void
    {
        $catRepo = new class implements CategoryRepositoryInterface {
            public function findAll(): array { return []; }
            public function findById(string $id): ?Category { return null; }
            public function exists(string $id): bool { return $id === 'cat-1'; }
        };

        $prodRepo = new class implements ProductRepositoryInterface {
            public ?Product $saved = null;
            public function findById(string $id): ?Product { return null; }
            public function findActiveById(string $id): ?Product { return null; }
            public function save(Product $product): void { $this->saved = $product; }
            public function delete(string $id): void {}
            public function search(?string $search, ?string $categoryId, int $page, int $size): array { return []; }
        };

        $useCase = new CreateProductUseCase($prodRepo, $catRepo);
        $product = $useCase->execute('Destornillador', '12000.00', 15, 'cat-1');

        $this->assertSame('Destornillador', $product->name());
        $this->assertSame(15, $product->stock());
        $this->assertSame('cat-1', $product->categoryId());
        $this->assertNotNull($prodRepo->saved);
    }

    public function test_rejects_product_creation_when_category_does_not_exist(): void
    {
        $catRepo = new class implements CategoryRepositoryInterface {
            public function findAll(): array { return []; }
            public function findById(string $id): ?Category { return null; }
            public function exists(string $id): bool { return false; }
        };

        $prodRepo = new class implements ProductRepositoryInterface {
            public function findById(string $id): ?Product { return null; }
            public function findActiveById(string $id): ?Product { return null; }
            public function save(Product $product): void {}
            public function delete(string $id): void {}
            public function search(?string $search, ?string $categoryId, int $page, int $size): array { return []; }
        };

        $useCase = new CreateProductUseCase($prodRepo, $catRepo);

        $this->expectException(BusinessRuleValidationException::class);
        $this->expectExceptionMessage("La categoría cat-inexistente no existe.");

        $useCase->execute('Tubo', '5000.00', 10, 'cat-inexistente');
    }
}
