<?php

declare(strict_types=1);

namespace App\Application\Ports\Inbound;

use DateTimeImmutable;

interface ListSalesPort
{
    /**
     * @param DateTimeImmutable $from
     * @param DateTimeImmutable $to
     * @param int $page
     * @param int $size
     * @return array{items: array<int, array>, total: int, page: int, size: int, totalPages: int}
     */
    public function execute(
        DateTimeImmutable $from,
        DateTimeImmutable $to,
        int $page,
        int $size
    ): array;
}
