<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repository;

use App\Application\Ports\Outbound\SaleRepositoryInterface;
use App\Domain\Entity\Sale;
use App\Infrastructure\Persistence\Mapper\SaleMapper;
use App\Infrastructure\Persistence\Model\SaleItemModel;
use App\Infrastructure\Persistence\Model\SaleModel;
use DateTimeImmutable;
use Illuminate\Support\Facades\DB;

final class SaleRepository implements SaleRepositoryInterface
{
    public function save(Sale $sale): void
    {
        $persistenceData = SaleMapper::toPersistence($sale);

        SaleModel::updateOrCreate(
            ['id' => $persistenceData['sale']['id']],
            $persistenceData['sale']
        );

        foreach ($persistenceData['items'] as $itemData) {
            SaleItemModel::updateOrCreate(
                ['id' => $itemData['id']],
                $itemData
            );
        }
    }

    public function findById(string $id): ?Sale
    {
        $model = SaleModel::where('id', $id)->first();
        if ($model === null) {
            return null;
        }

        $items = SaleItemModel::where('sale_id', $id)->get();

        return SaleMapper::toDomain($model, $items);
    }

    public function findBetweenDates(
        DateTimeImmutable $from,
        DateTimeImmutable $to,
        int $page,
        int $size
    ): array {
        // D-C2: from inclusivo, to exclusivo
        $fromStr = $from->format('Y-m-d H:i:s.u');
        $toStr = $to->format('Y-m-d H:i:s.u');

        $query = SaleModel::query()
            ->where('sold_at', '>=', $fromStr)
            ->where('sold_at', '<', $toStr);

        $total = $query->count();
        $totalPages = $size === 0 ? 0 : (int) ceil($total / $size);

        $salesModels = $query->orderBy('sold_at', 'desc')
            ->offset(($page - 1) * $size)
            ->limit($size)
            ->get();

        $saleIds = $salesModels->pluck('id')->all();
        $allItemModels = SaleItemModel::whereIn('sale_id', $saleIds)->get()->groupBy('sale_id');

        $items = [];
        foreach ($salesModels as $saleModel) {
            $itemsModels = $allItemModels->get($saleModel->id, collect());
            $sale = SaleMapper::toDomain($saleModel, $itemsModels);

            $saleItemsView = [];
            foreach ($sale->items() as $item) {
                $saleItemsView[] = [
                    'productId' => $item->productId(),
                    'productName' => $item->productName(),
                    'quantity' => $item->quantity()->value(),
                    'unitPrice' => (float) (string) $item->unitPrice()->amount(),
                    'subtotal' => (float) (string) $item->subtotal()->amount(),
                ];
            }

            $items[] = [
                'id' => $sale->id(),
                'soldAt' => $sale->soldAt()->format('Y-m-d\TH:i:s.u\+00:00'),
                'soldBy' => $sale->soldByUsername(),
                'total' => (float) (string) $sale->total()->amount(),
                'currency' => 'COP',
                'items' => $saleItemsView,
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

    public function getSalesReport(DateTimeImmutable $from, DateTimeImmutable $to): array
    {
        $fromStr = $from->format('Y-m-d H:i:s.u');
        $toStr = $to->format('Y-m-d H:i:s.u');

        // Número de ventas en el período
        $salesCount = SaleModel::where('sold_at', '>=', $fromStr)
            ->where('sold_at', '<', $toStr)
            ->count();

        // Agregación resuelta en la base de datos (CA-06.5, Q9)
        $rawRows = DB::table('sale_item')
            ->join('sale', 'sale.id', '=', 'sale_item.sale_id')
            ->where('sale.sold_at', '>=', $fromStr)
            ->where('sale.sold_at', '<', $toStr)
            ->select([
                'sale_item.product_id',
                'sale_item.category_name',
                DB::raw('MAX(sale_item.product_name) AS product_name'),
                DB::raw('SUM(sale_item.quantity) AS units_sold'),
                DB::raw('SUM(sale_item.quantity * sale_item.unit_price) AS revenue')
            ])
            ->groupBy('sale_item.product_id', 'sale_item.category_name')
            ->orderByDesc('revenue')
            ->get();

        $rows = [];
        $grandTotal = 0.0;

        foreach ($rawRows as $r) {
            $revenue = (float) $r->revenue;
            $grandTotal += $revenue;

            $rows[] = [
                'productId' => (string) $r->product_id,
                'productName' => (string) $r->product_name,
                'categoryName' => (string) $r->category_name,
                'unitsSold' => (int) $r->units_sold,
                'revenue' => $revenue,
            ];
        }

        return [
            'from' => $from->format('Y-m-d\TH:i:s.u\+00:00'),
            'to' => $to->format('Y-m-d\TH:i:s.u\+00:00'),
            'salesCount' => $salesCount,
            'grandTotal' => $grandTotal,
            'currency' => 'COP',
            'rows' => $rows,
        ];
    }
}
