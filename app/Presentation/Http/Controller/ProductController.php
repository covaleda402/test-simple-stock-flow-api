<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Application\UseCase\CreateProductUseCase;
use App\Application\Ports\Outbound\ProductRepositoryInterface;
use InvalidArgumentException;

final class ProductController extends Controller
{
    public function __construct(
        private CreateProductUseCase $createProductUseCase,
        private ProductRepositoryInterface $productRepository
    ) {}

    public function store(Request $request): JsonResponse
    {
        try {
            $data = $request->validate([
                'id' => 'required|string',
                'name' => 'required|string',
                'price' => 'required|numeric',
                'stock' => 'required|integer|min:0',
            ]);

            $product = $this->createProductUseCase->execute(
                $data['id'],
                $data['name'],
                $data['price'],
                $data['stock']
            );

            return response()->json([
                'id' => $product->id(),
                'name' => $product->name(),
                'price' => $product->price()->amount()->toFloat(),
                'stock' => $product->stock()->value()
            ], 201);
            
        } catch (InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function index(): JsonResponse
    {
        $products = $this->productRepository->findAll();
        
        $response = array_map(function ($product) {
            return [
                'id' => $product->id(),
                'name' => $product->name(),
                'price' => $product->price()->amount()->toFloat(),
                'stock' => $product->stock()->value()
            ];
        }, $products);

        return response()->json($response, 200);
    }
}