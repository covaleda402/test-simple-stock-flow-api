<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller;

use App\Application\Ports\Inbound\LoginPort;
use App\Application\Ports\Inbound\RegisterSellerPort;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

final class AuthController extends Controller
{
    public function __construct(
        private LoginPort $loginPort,
        private RegisterSellerPort $registerSellerPort
    ) {}

    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $result = $this->loginPort->execute($data['username'], $data['password']);

        return response()->json($result, 200);
    }

    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
            'role' => 'required|string',
        ]);

        $userId = $this->registerSellerPort->execute(
            $data['username'],
            $data['password'],
            $data['role']
        );

        // D-C6: 201 Created con {"id": "<uuid>"} y SIN cabecera Location
        return response()->json(['id' => $userId], 201);
    }
}
