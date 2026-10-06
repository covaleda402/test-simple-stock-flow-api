<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\Ports\Inbound\DeleteProductPort;
use App\Application\Ports\Outbound\FileStorageInterface;
use App\Application\Ports\Outbound\ProductRepositoryInterface;

final class DeleteProductUseCase implements DeleteProductPort
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private FileStorageInterface $fileStorage
    ) {}

    public function execute(string $id): void
    {
        $product = $this->productRepository->findById($id);
        if ($product === null) {
            return;
        }

        $imageKey = $product->imageKey();
        if ($imageKey !== null) {
            $product->attachImage(null);
            $this->productRepository->save($product);
            $this->fileStorage->delete($imageKey);
        }

        $this->productRepository->delete($id);
    }
}
