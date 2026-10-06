<?php

declare(strict_types=1);

namespace App\Application\Ports\Inbound;

use App\Domain\Entity\Product;

interface CreateProductPort
{
    public function execute(string $name, string|int $price, int $stock, string $categoryId): Product;
}
