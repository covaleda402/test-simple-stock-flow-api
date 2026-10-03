<?php

declare(strict_types=1);

namespace App\Application\Ports\Outbound;

use App\Domain\Entity\Product;

interface ProductRepositoryInterface
{
    public function findById(string $id): ?Product;
    
    public function save(Product $product): void;
    
    /**
     * @return Product[]
     */
    public function findAll(): array;
}