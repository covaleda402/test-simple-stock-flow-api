<?php

declare(strict_types=1);

namespace App\Application\Ports\Outbound;

use App\Domain\Entity\Category;

interface CategoryRepositoryInterface
{
    /**
     * @return array<int, Category>
     */
    public function findAll(): array;

    public function findById(string $id): ?Category;

    public function exists(string $id): bool;
}
