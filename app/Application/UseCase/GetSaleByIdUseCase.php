<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\Ports\Inbound\GetSaleByIdPort;
use App\Application\Ports\Outbound\SaleRepositoryInterface;

final class GetSaleByIdUseCase implements GetSaleByIdPort
{
    public function __construct(
        private SaleRepositoryInterface $saleRepository
    ) {}

    public function execute(string $id): ?array
    {
        $sale = $this->saleRepository->findById($id);
        if ($sale === null) {
            return null;
        }

        $items = [];
        foreach ($sale->items() as $item) {
            $items[] = [
                'productId' => $item->productId(),
                'productName' => $item->productName(),
                'quantity' => $item->quantity()->value(),
                'unitPrice' => (float) (string) $item->unitPrice()->amount(),
                'subtotal' => (float) (string) $item->subtotal()->amount(),
            ];
        }

        return [
            'id' => $sale->id(),
            'soldAt' => $sale->soldAt()->format('Y-m-d\TH:i:s.u\+00:00'),
            'soldBy' => $sale->soldByUsername(),
            'total' => (float) (string) $sale->total()->amount(),
            'currency' => 'COP',
            'items' => $items,
        ];
    }
}
