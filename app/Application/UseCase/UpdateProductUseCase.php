<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\Ports\Inbound\UpdateProductPort;
use App\Application\Ports\Outbound\CategoryRepositoryInterface;
use App\Application\Ports\Outbound\ProductRepositoryInterface;
use App\Domain\Exception\BusinessRuleValidationException;
use App\Domain\ValueObject\Money;

final class UpdateProductUseCase implements UpdateProductPort
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private CategoryRepositoryInterface $categoryRepository
    ) {}

    public function execute(string $id, string $name, string|int $price, int $stock, string $categoryId): void
    {
        $trimmedCategory = trim($categoryId);
        if (!$this->categoryRepository->exists($trimmedCategory)) {
            throw new BusinessRuleValidationException("La categoría {$trimmedCategory} no existe.");
        }

        $product = $this->productRepository->findById($id);
        if ($product === null) {
            throw new BusinessRuleValidationException("El producto {$id} no existe.");
        }

        $product->rename($name);
        $product->changePrice(new Money($price));
        $product->setCategory($trimmedCategory);

        $difference = $stock - $product->stock();
        if ($difference > 0) {
            $product->restock($difference);
        } elseif ($difference < 0) {
            $product->withdraw(new \App\Domain\ValueObject\Quantity(abs($difference)));
        }

        $this->productRepository->save($product);
    }
}
