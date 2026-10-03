<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repository;

use App\Application\Ports\Outbound\ProductRepositoryInterface;
use App\Domain\Entity\Product;
use App\Domain\ValueObject\Money;
use App\Domain\ValueObject\Quantity;
use App\Infrastructure\Persistence\Model\ProductModel;

final class ProductRepository implements ProductRepositoryInterface
{
    public function findById(string $id): ?Product
    {
        $model = ProductModel::find($id);
        
        if ($model === null) {
            return null;
        }

        return new Product(
            $model->id,
            $model->name,
            new Money($model->price),
            new Quantity((int) $model->stock)
        );
    }

    public function save(Product $product): void
    {
        ProductModel::updateOrCreate(
            ['id' => $product->id()],
            [
                'name' => $product->name(),
                // Extraemos el valor primitivo solo para guardar en DB
                'price' => $product->price()->amount()->toFloat(),
                'stock' => $product->stock()->value()
            ]
        );
    }

    public function findAll(): array
    {
        return ProductModel::all()->map(function (ProductModel $model) {
            return new Product(
                $model->id,
                $model->name,
                new Money($model->price),
                new Quantity((int) $model->stock)
            );
        })->toArray();
    }
}