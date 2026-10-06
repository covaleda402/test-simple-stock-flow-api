<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repository;

use App\Application\Ports\Outbound\ProductRepositoryInterface;
use App\Domain\Entity\Product;
use App\Infrastructure\Persistence\Mapper\ProductMapper;
use App\Infrastructure\Persistence\Model\CategoryModel;
use App\Infrastructure\Persistence\Model\ProductModel;
use Illuminate\Support\Facades\DB;

final class ProductRepository implements ProductRepositoryInterface
{
    public function findById(string $id): ?Product
    {
        $model = ProductModel::where('id', $id)->first();
        if ($model === null) {
            return null;
        }

        return ProductMapper::toDomain($model);
    }

    public function findActiveById(string $id): ?Product
    {
        $model = ProductModel::where('id', $id)
            ->whereNull('deleted_at')
            ->first();

        if ($model === null) {
            return null;
        }

        return ProductMapper::toDomain($model);
    }

    public function save(Product $product): void
    {
        $data = ProductMapper::toPersistence($product);

        ProductModel::updateOrCreate(
            ['id' => $product->id()],
            $data
        );
    }

    public function delete(string $id): void
    {
        ProductModel::where('id', $id)->update([
            'deleted_at' => now(),
        ]);
    }

    public function search(?string $search, ?string $categoryId, int $page, int $size): array
    {
        $query = ProductModel::query()
            ->whereNull('deleted_at');

        if ($categoryId !== null && trim($categoryId) !== '') {
            $query->where('category_id', trim($categoryId));
        }

        if ($search !== null && trim($search) !== '') {
            $escaped = addcslashes(trim($search), '%_\\');
            $query->where('name', 'LIKE', '%' . $escaped . '%');
        }

        $total = $query->count();
        $totalPages = $size === 0 ? 0 : (int) ceil($total / $size);

        $models = $query->orderBy('name', 'asc')
            ->offset(($page - 1) * $size)
            ->limit($size)
            ->get();

        // Obtener nombres de categoría de una sola vez
        $categoryIds = $models->pluck('category_id')->unique()->all();
        $categories = CategoryModel::whereIn('id', $categoryIds)->pluck('name', 'id')->all();

        $items = [];
        foreach ($models as $m) {
            $catName = $categories[$m->category_id] ?? 'General';
            $imageUrl = $m->image_key !== null ? '/media/' . $m->image_key : null;

            $items[] = [
                'id' => $m->id,
                'name' => $m->name,
                'price' => (float) (string) $m->price,
                'currency' => 'COP',
                'stock' => (int) $m->stock,
                'categoryId' => $m->category_id,
                'categoryName' => $catName,
                'imageUrl' => $imageUrl,
            ];
        }

        return [
            'items' => $items,
            'page' => $page,
            'size' => $size,
            'total' => $total,
            'totalPages' => $totalPages,
        ];
    }
}