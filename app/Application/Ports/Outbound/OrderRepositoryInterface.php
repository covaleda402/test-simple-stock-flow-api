<?php

declare(strict_types=1);

namespace App\Application\Ports\Outbound;

use App\Domain\Entity\Order;

interface OrderRepositoryInterface
{
    public function save(Order $order): void;
    
    public function findById(string $id): ?Order;
    
    /**
     * @return Order[]
     */
    public function findAll(): array;
}