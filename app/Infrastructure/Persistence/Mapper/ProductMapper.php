<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Mapper;

use App\Domain\Entity\Product;
use App\Domain\ValueObject\Money;
use App\Infrastructure\Persistence\Model\ProductModel;

final class ProductMapper
{
    public static function toDomain(ProductModel $model): Product
    {
        return new Product(
            $model->id,
            $model->name,
            new Money((string) $model->price),
            (int) $model->stock,
            $model->category_id,
            $model->image_key
        );
    }

    public static function toPersistence(Product $domain): array
    {
        return [
            'id' => $domain->id(),
            'name' => $domain->name(),
            'price' => (string) $domain->price()->amount(),
            'stock' => $domain->stock(),
            'category_id' => $domain->categoryId(),
            'image_key' => $domain->imageKey(),
        ];
    }
}
