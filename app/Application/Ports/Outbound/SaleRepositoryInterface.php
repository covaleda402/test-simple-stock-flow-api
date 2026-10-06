<?php

declare(strict_types=1);

namespace App\Application\Ports\Outbound;

use App\Domain\Entity\Sale;
use DateTimeImmutable;

interface SaleRepositoryInterface
{
    public function save(Sale $sale): void;

    public function findById(string $id): ?Sale;

    /**
     * @param DateTimeImmutable $from
     * @param DateTimeImmutable $to
     * @param int $page
     * @param int $size
     * @return array{items: array<int, array>, total: int, page: int, size: int, totalPages: int}
     */
    public function findBetweenDates(
        DateTimeImmutable $from,
        DateTimeImmutable $to,
        int $page,
        int $size
    ): array;

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
    public function getSalesReport(DateTimeImmutable $from, DateTimeImmutable $to): array;
}
