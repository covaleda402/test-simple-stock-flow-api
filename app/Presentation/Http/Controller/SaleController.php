<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller;

use App\Application\Ports\Inbound\GetSaleByIdPort;
use App\Application\Ports\Inbound\ListSalesPort;
use App\Application\Ports\Inbound\PlaceSalePort;
use DateTimeImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class SaleController extends Controller
{
    public function __construct(
        private PlaceSalePort $placeSalePort,
        private ListSalesPort $listSalesPort,
        private GetSaleByIdPort $getSaleByIdPort
    ) {}

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'lines' => 'required|array',
            'lines.*.productId' => 'required|string',
            'lines.*.quantity' => 'required|integer',
        ]);

        $username = (string) $request->attributes->get('auth_username', 'vendedor');
        $userId = (string) $request->attributes->get('auth_user_id', '11111111-1111-4111-8111-111111111111');

        $saleId = $this->placeSalePort->execute(
            $data['lines'],
            $username,
            $userId
        );

        return response()
            ->json(['id' => $saleId], 201)
            ->header('Location', '/api/sales/' . $saleId);
    }

    public function index(Request $request): JsonResponse
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

        $page = (int) $request->query('page', 1);
        $size = (int) $request->query('size', 20);

        $result = $this->listSalesPort->execute($from, $to, $page, $size);

        return response()->json($result, 200);
    }

    public function show(string $id): JsonResponse
    {
        $sale = $this->getSaleByIdPort->execute($id);
        if ($sale === null) {
            throw new NotFoundHttpException();
        }

        return response()->json($sale, 200);
    }
}
