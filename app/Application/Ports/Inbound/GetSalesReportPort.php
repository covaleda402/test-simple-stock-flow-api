<?php

declare(strict_types=1);

namespace App\Application\Ports\Inbound;

use DateTimeImmutable;

interface GetSalesReportPort
{
    /**
     * @param DateTimeImmutable $from
     * @param DateTimeImmutable $to
     * @return array{
     *     from: string,
     *     to: string,
     *     salesCount: int,
     *     grandTotal: float|int,
     *     currency: string,
     *     rows: array<int, array{
     *         productId: string,
     *         productName: string,
     *         categoryName: string,
     *         unitsSold: int,
     *         revenue: float|int
     *     }>
     * }
     */
    public function execute(DateTimeImmutable $from, DateTimeImmutable $to): array;
}
