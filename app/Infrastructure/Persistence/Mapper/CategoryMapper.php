<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Mapper;

use App\Domain\Entity\Category;
use App\Infrastructure\Persistence\Model\CategoryModel;

final class CategoryMapper
{
    public static function toDomain(CategoryModel $model): Category
    {
        return new Category(
            $model->id,
            $model->name
        );
    }

    public static function toPersistence(Category $domain): array
    {
        return [
            'id' => $domain->id(),
            'name' => $domain->name(),
        ];
    }
}
