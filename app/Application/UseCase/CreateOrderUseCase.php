<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\Ports\Outbound\OrderRepositoryInterface;
use App\Application\Ports\Outbound\ProductRepositoryInterface;
use App\Domain\Entity\Order;
use App\Domain\ValueObject\Quantity;
use InvalidArgumentException;

final class CreateOrderUseCase
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private ProductRepositoryInterface $productRepository
    ) {}

    public function execute(string $orderId, string $productId, int $quantityRaw): Order
    {
        $product = $this->productRepository->findById($productId);
        
        if ($product === null) {
            throw new InvalidArgumentException("El producto con ID {$productId} no existe.");
        }

        $quantity = new Quantity($quantityRaw);
        
        // Restar stock usando la regla innegociable de la Entidad pura
        $product->removeStock($quantity);

        $order = new Order(
            $orderId,
            $productId,
            $quantity,
            $product->price() // Hereda el precio unitario exacto del producto
        );

        // Guardar la actualización del inventario y la nueva orden
        $this->productRepository->save($product);
        $this->orderRepository->save($order);

        return $order;
    }
}