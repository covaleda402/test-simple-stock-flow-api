<?php

declare(strict_types=1);

namespace App\Application\Ports\Inbound;

interface ListProductsPort
{
    /**
     * @param string|null $search
     * @param string|null $categoryId
     * @param int $page
     * @param int $size
     * @return array{items: array<int, array>, total: int, page: int, size: int, totalPages: int}
     */
    public function execute(?string $search, ?string $categoryId, int $page, int $size): array;
}
