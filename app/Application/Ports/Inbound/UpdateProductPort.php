<?php

declare(strict_types=1);

namespace App\Application\Ports\Inbound;

interface UpdateProductPort
{
    public function execute(string $id, string $name, string|int $price, int $stock, string $categoryId): void;
}
