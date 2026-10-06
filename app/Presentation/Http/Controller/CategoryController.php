<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller;

use App\Application\Ports\Inbound\ListCategoriesPort;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

final class CategoryController extends Controller
{
    public function __construct(
        private ListCategoriesPort $listCategoriesPort
    ) {}

    public function index(): JsonResponse
    {
        $categories = $this->listCategoriesPort->execute();

        return response()->json($categories, 200);
    }
}
