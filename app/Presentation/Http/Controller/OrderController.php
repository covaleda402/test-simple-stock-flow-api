<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Application\UseCase\CreateOrderUseCase;
use App\Application\Ports\Outbound\OrderRepositoryInterface;
use InvalidArgumentException;

final class OrderController extends Controller
{
    public function __construct(
        private CreateOrderUseCase $createOrderUseCase,
        private OrderRepositoryInterface $orderRepository
    ) {}

    public function store(Request $request): JsonResponse
    {
        try {
            $data = $request->validate([
                'id' => 'required|string',
                'product_id' => 'required|string',
                'quantity' => 'required|integer|min:1',
            ]);

            $order = $this->createOrderUseCase->execute(
                $data['id'],
                $data['product_id'],
                $data['quantity']
            );

            return response()->json([
                'id' => $order->id(),
                'product_id' => $order->productId(),
                'quantity' => $order->quantity()->value(),
                'total_price' => $order->totalPrice()->amount()->toFloat(),
                'status' => $order->status(),
                'created_at' => $order->createdAt()->format('Y-m-d H:i:s')
            ], 201);
            
        } catch (InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function index(): JsonResponse
    {
        $orders = $this->orderRepository->findAll();
        
        $response = array_map(function ($order) {
            return [
                'id' => $order->id(),
                'product_id' => $order->productId(),
                'quantity' => $order->quantity()->value(),
                'total_price' => $order->totalPrice()->amount()->toFloat(),
                'status' => $order->status(),
                'created_at' => $order->createdAt()->format('Y-m-d H:i:s')
            ];
        }, $orders);

        return response()->json($response, 200);
    }
}