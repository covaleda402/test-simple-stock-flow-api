<?php

declare(strict_types=1);

namespace App\Application\Ports\Inbound;

interface PlaceSalePort
{
    /**
     * @param array<int, array{productId: string, quantity: int}> $lines
     * @param string $soldByUsername
     * @param string $soldByUserId
     * @return string sale id
     */
    public function execute(array $lines, string $soldByUsername, string $soldByUserId): string;
}
