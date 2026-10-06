<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller;

use App\Application\Ports\Inbound\CreateProductPort;
use App\Application\Ports\Inbound\DeleteProductPort;
use App\Application\Ports\Inbound\GetProductByIdPort;
use App\Application\Ports\Inbound\ListProductsPort;
use App\Application\Ports\Inbound\UpdateProductPort;
use App\Application\Ports\Inbound\UploadProductImagePort;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ProductController extends Controller
{
    public function __construct(
        private ListProductsPort $listProductsPort,
        private GetProductByIdPort $getProductByIdPort,
        private CreateProductPort $createProductPort,
        private UpdateProductPort $updateProductPort,
        private DeleteProductPort $deleteProductPort,
        private UploadProductImagePort $uploadProductImagePort
    ) {}

    public function index(Request $request): JsonResponse
    {
        $search = $request->query('search');
        $categoryId = $request->query('categoryId');
        $page = (int) $request->query('page', 1);
        $size = (int) $request->query('size', 20);

        $result = $this->listProductsPort->execute(
            is_string($search) ? $search : null,
            is_string($categoryId) ? $categoryId : null,
            $page,
            $size
        );

        return response()->json($result, 200);
    }

    public function show(string $id): JsonResponse
    {
        $product = $this->getProductByIdPort->execute($id);
        if ($product === null) {
            throw new NotFoundHttpException();
        }

        return response()->json($product, 200);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'categoryId' => 'required|string',
        ]);

        $product = $this->createProductPort->execute(
            $data['name'],
            $data['price'],
            (int) $data['stock'],
            $data['categoryId']
        );

        return response()
            ->json(['id' => $product->id()], 201)
            ->header('Location', '/api/products/' . $product->id());
    }

    public function update(Request $request, string $id): Response
    {
        $data = $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'categoryId' => 'required|string',
        ]);

        $this->updateProductPort->execute(
            $id,
            $data['name'],
            $data['price'],
            (int) $data['stock'],
            $data['categoryId']
        );

        return response('', 204);
    }

    public function destroy(string $id): Response
    {
        $this->deleteProductPort->execute($id);

        return response('', 204);
    }

    public function uploadImage(Request $request, string $id): JsonResponse
    {
        if (!$request->hasFile('file')) {
            return response()->json([
                'title' => 'Datos de entrada no válidos',
                'status' => 400,
                'detail' => 'Datos de entrada no válidos: file.',
                'errors' => ['file' => ['Field required']],
            ], 400);
        }

        $file = $request->file('file');
        $binary = file_get_contents($file->getRealPath());
        $mime = $file->getMimeType() ?: 'image/jpeg';

        $url = $this->uploadProductImagePort->execute($id, $binary, $mime);

        return response()->json(['url' => $url], 200);
    }
}