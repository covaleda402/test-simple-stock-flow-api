<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

final class HealthController extends Controller
{
    public function check(): JsonResponse
    {
        return response()->json(['status' => 'ok'], 200);
    }
}
