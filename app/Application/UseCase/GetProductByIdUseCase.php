<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\Ports\Inbound\GetProductByIdPort;
use App\Application\Ports\Outbound\CategoryRepositoryInterface;
use App\Application\Ports\Outbound\ProductRepositoryInterface;

final class GetProductByIdUseCase implements GetProductByIdPort
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private CategoryRepositoryInterface $categoryRepository
    ) {}

    public function execute(string $id): ?array
    {
        $product = $this->productRepository->findById($id);
        if ($product === null) {
            return null;
        }

        $category = $this->categoryRepository->findById($product->categoryId());
        $categoryName = $category !== null ? $category->name() : 'Desconocida';

        $imageUrl = $product->imageKey() !== null ? '/media/' . $product->imageKey() : null;

        return [
            'id' => $product->id(),
            'name' => $product->name(),
            'price' => (float) (string) $product->price()->amount(),
            'currency' => $product->price()->currency(),
            'stock' => $product->stock(),
            'categoryId' => $product->categoryId(),
            'categoryName' => $categoryName,
            'imageUrl' => $imageUrl,
        ];
    }
}
