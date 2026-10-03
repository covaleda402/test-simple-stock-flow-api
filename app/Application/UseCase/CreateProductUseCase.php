<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\Ports\Outbound\ProductRepositoryInterface;
use App\Domain\Entity\Product;
use App\Domain\ValueObject\Money;
use App\Domain\ValueObject\Quantity;
use InvalidArgumentException;

final class CreateProductUseCase
{
    public function __construct(
        private ProductRepositoryInterface $productRepository
    ) {}

    public function execute(string $id, string $name, float|string $price, int $stock): Product
    {
        if ($this->productRepository->findById($id) !== null) {
            throw new InvalidArgumentException("El producto con ID {$id} ya existe.");
        }

        $product = new Product(
            $id,
            $name,
            new Money($price), // Manejo exacto del dinero (ADR-006)
            new Quantity($stock)
        );

        $this->productRepository->save($product);

        return $product;
    }
}