<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repository;

use App\Application\Ports\Outbound\CategoryRepositoryInterface;
use App\Domain\Entity\Category;
use App\Infrastructure\Persistence\Mapper\CategoryMapper;
use App\Infrastructure\Persistence\Model\CategoryModel;

final class CategoryRepository implements CategoryRepositoryInterface
{
    public function findAll(): array
    {
        return CategoryModel::orderBy('name', 'asc')
            ->get()
            ->map(static fn (CategoryModel $m) => CategoryMapper::toDomain($m))
            ->all();
    }

    public function findById(string $id): ?Category
    {
        $model = CategoryModel::where('id', $id)->first();
        if ($model === null) {
            return null;
        }

        return CategoryMapper::toDomain($model);
    }

    public function exists(string $id): bool
    {
        return CategoryModel::where('id', $id)->exists();
    }
}
