<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repository;

use App\Application\Ports\Outbound\OrderRepositoryInterface;
use App\Domain\Entity\Order;
use App\Domain\ValueObject\Money;
use App\Domain\ValueObject\Quantity;
use App\Infrastructure\Persistence\Model\OrderModel;
use DateTimeImmutable;

final class OrderRepository implements OrderRepositoryInterface
{
    public function save(Order $order): void
    {
        OrderModel::updateOrCreate(
            ['id' => $order->id()],
            [
                'product_id' => $order->productId(),
                'quantity' => $order->quantity()->value(),
                'total_price' => $order->totalPrice()->amount()->toFloat(),
                'status' => $order->status(),
                'created_at' => $order->createdAt()->format('Y-m-d H:i:s')
            ]
        );
    }

    public function findById(string $id): ?Order
    {
        $model = OrderModel::find($id);
        
        if ($model === null) {
            return null;
        }

        // Reconstruye el precio unitario a partir del total para pasarlo al constructor puro
        $unitPrice = $model->total_price / $model->quantity;

        return new Order(
            $model->id,
            $model->product_id,
            new Quantity((int) $model->quantity),
            new Money($unitPrice),
            new DateTimeImmutable($model->created_at)
        );
    }

    public function findAll(): array
    {
        return OrderModel::all()->map(function (OrderModel $model) {
            $unitPrice = $model->total_price / $model->quantity;
            return new Order(
                $model->id,
                $model->product_id,
                new Quantity((int) $model->quantity),
                new Money($unitPrice),
                new DateTimeImmutable($model->created_at)
            );
        })->toArray();
    }
}