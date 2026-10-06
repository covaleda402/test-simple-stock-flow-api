<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\Ports\Inbound\ListProductsPort;
use App\Application\Ports\Outbound\ProductRepositoryInterface;

final class ListProductsUseCase implements ListProductsPort
{
    public function __construct(
        private ProductRepositoryInterface $productRepository
    ) {}

    public function execute(?string $search, ?string $categoryId, int $page, int $size): array
    {
        $sanitizedPage = max(1, $page);
        $sanitizedSize = $size < 1 ? 20 : min(100, $size);

        return $this->productRepository->search($search, $categoryId, $sanitizedPage, $sanitizedSize);
    }
}
