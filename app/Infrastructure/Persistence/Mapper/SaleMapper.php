<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Mapper;

use App\Domain\Entity\Sale;
use App\Domain\Entity\SaleItem;
use App\Domain\ValueObject\Money;
use App\Domain\ValueObject\Quantity;
use App\Infrastructure\Persistence\Model\SaleItemModel;
use App\Infrastructure\Persistence\Model\SaleModel;
use DateTimeImmutable;
use DateTimeZone;

final class SaleMapper
{
    /**
     * @param SaleModel $model
     * @param iterable<SaleItemModel> $items
     * @return Sale
     */
    public static function toDomain(SaleModel $model, iterable $items): Sale
    {
        $domainItems = [];
        foreach ($items as $itemModel) {
            $domainItems[] = new SaleItem(
                $itemModel->id,
                $itemModel->sale_id,
                $itemModel->product_id,
                $itemModel->product_name,
                $itemModel->category_name,
                new Quantity((int) $itemModel->quantity),
                new Money((string) $itemModel->unit_price)
            );
        }

        $soldAt = $model->sold_at instanceof \DateTimeInterface
            ? DateTimeImmutable::createFromInterface($model->sold_at)
            : new DateTimeImmutable((string) $model->sold_at, new DateTimeZone('UTC'));

        return new Sale(
            $model->id,
            $model->sold_by_username,
            $model->sold_by_user_id,
            $soldAt,
            $domainItems
        );
    }

    /**
     * @param Sale $sale
     * @return array{sale: array<string, mixed>, items: array<int, array<string, mixed>>}
     */
    public static function toPersistence(Sale $sale): array
    {
        $saleData = [
            'id' => $sale->id(),
            'sold_at' => $sale->soldAt()->format('Y-m-d H:i:s.u'),
            'sold_by_username' => $sale->soldByUsername(),
            'sold_by_user_id' => $sale->soldByUserId(),
        ];

        $itemsData = [];
        foreach ($sale->items() as $item) {
            $itemsData[] = [
                'id' => $item->id(),
                'sale_id' => $item->saleId(),
                'product_id' => $item->productId(),
                'product_name' => $item->productName(),
                'category_name' => $item->categoryName(),
                'quantity' => $item->quantity()->value(),
                'unit_price' => (string) $item->unitPrice()->amount(),
            ];
        }

        return [
            'sale' => $saleData,
            'items' => $itemsData,
        ];
    }
}
