<?php

declare(strict_types=1);

namespace App\Application\Ports\Outbound;

use App\Domain\Entity\Product;

interface ProductRepositoryInterface
{
    public function findById(string $id): ?Product;

    public function findActiveById(string $id): ?Product;

    public function save(Product $product): void;

    public function delete(string $id): void;

    /**
     * @param string|null $search
     * @param string|null $categoryId
     * @param int $page
     * @param int $size
     * @return array{items: array<int, array>, total: int, page: int, size: int, totalPages: int}
     */
    public function search(?string $search, ?string $categoryId, int $page, int $size): array;
}