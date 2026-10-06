<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\Ports\Inbound\CreateProductPort;
use App\Application\Ports\Outbound\CategoryRepositoryInterface;
use App\Application\Ports\Outbound\ProductRepositoryInterface;
use App\Domain\Entity\Product;
use App\Domain\Exception\BusinessRuleValidationException;
use App\Domain\ValueObject\Money;

final class CreateProductUseCase implements CreateProductPort
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private CategoryRepositoryInterface $categoryRepository
    ) {}

    public function execute(string $name, string|int $price, int $stock, string $categoryId): Product
    {
        $trimmedCategory = trim($categoryId);
        if (!$this->categoryRepository->exists($trimmedCategory)) {
            throw new BusinessRuleValidationException("La categoría {$trimmedCategory} no existe.");
        }

        $id = self::generateUuidV4();

        $product = new Product(
            $id,
            $name,
            new Money($price),
            $stock,
            $trimmedCategory
        );

        $this->productRepository->save($product);

        return $product;
    }

    private static function generateUuidV4(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}