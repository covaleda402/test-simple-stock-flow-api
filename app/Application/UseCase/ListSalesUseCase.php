<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\Ports\Inbound\ListSalesPort;
use App\Application\Ports\Outbound\SaleRepositoryInterface;
use App\Domain\Exception\BusinessRuleValidationException;
use DateTimeImmutable;

final class ListSalesUseCase implements ListSalesPort
{
    public function __construct(
        private SaleRepositoryInterface $saleRepository
    ) {}

    public function execute(
        DateTimeImmutable $from,
        DateTimeImmutable $to,
        int $page,
        int $size
    ): array {
        if ($to < $from) {
            throw new BusinessRuleValidationException("La fecha final no puede ser anterior a la inicial.");
        }

        $sanitizedPage = max(1, $page);
        $sanitizedSize = $size < 1 ? 20 : min(100, $size);

        return $this->saleRepository->findBetweenDates($from, $to, $sanitizedPage, $sanitizedSize);
    }
}
