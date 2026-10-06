<?php

declare(strict_types=1);

namespace App\Application\Ports\Inbound;

interface GetProductByIdPort
{
    /**
     * @param string $id
     * @return array<string, mixed>|null
     */
    public function execute(string $id): ?array;
}
