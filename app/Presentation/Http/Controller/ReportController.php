<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller;

use App\Application\Ports\Inbound\GetSalesReportPort;
use DateTimeImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

final class ReportController extends Controller
{
    public function __construct(
        private GetSalesReportPort $getSalesReportPort
    ) {}

    public function sales(Request $request): JsonResponse
    {
        $fromRaw = $request->query('from');
        $toRaw = $request->query('to');

        if (!is_string($fromRaw) || !is_string($toRaw) || trim($fromRaw) === '' || trim($toRaw) === '') {
            return response()->json([
                'title' => 'Datos de entrada no válidos',
                'status' => 400,
                'detail' => 'Datos de entrada no válidos: from, to.',
                'errors' => [
                    'from' => ['Field required'],
                    'to' => ['Field required'],
                ],
            ], 400);
        }

        try {
            $from = new DateTimeImmutable($fromRaw);
            $to = new DateTimeImmutable($toRaw);
        } catch (\Throwable $e) {
            return response()->json([
                'title' => 'Datos de entrada no válidos',
                'status' => 400,
                'detail' => 'Datos de entrada no válidos: formato de fecha no válido.',
                'errors' => [
                    'from' => ['Invalid ISO 8601 format'],
                ],
            ], 400);
        }

        $report = $this->getSalesReportPort->execute($from, $to);

        return response()->json($report, 200);
    }
}
