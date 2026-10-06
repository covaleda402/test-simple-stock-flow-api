<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\Ports\Inbound\GetSalesReportPort;
use App\Application\Ports\Outbound\SaleRepositoryInterface;
use App\Domain\Exception\BusinessRuleValidationException;
use DateTimeImmutable;

final class GetSalesReportUseCase implements GetSalesReportPort
{
    public function __construct(
        private SaleRepositoryInterface $saleRepository
    ) {}

    public function execute(DateTimeImmutable $from, DateTimeImmutable $to): array
    {
        if ($to < $from) {
            throw new BusinessRuleValidationException("La fecha final no puede ser anterior a la inicial.");
        }

        return $this->saleRepository->getSalesReport($from, $to);
    }
}
