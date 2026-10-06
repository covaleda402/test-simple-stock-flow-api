<?php

declare(strict_types=1);

namespace App\Presentation\Http\Middleware;

use App\Application\Ports\Outbound\TokenGeneratorInterface;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class JwtAuthMiddleware
{
    public function __construct(
        private TokenGeneratorInterface $tokenGenerator
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $authHeader = $request->header('Authorization');
        if ($authHeader === null || !str_starts_with($authHeader, 'Bearer ')) {
            return response('', 401)->header('WWW-Authenticate', 'Bearer');
        }

        $token = substr($authHeader, 7);
        $payload = $this->tokenGenerator->validate($token);

        if ($payload === null) {
            return response('', 401)->header('WWW-Authenticate', 'Bearer error="invalid_token"');
        }

        $request->attributes->set('auth_user_id', $payload['sub']);
        $request->attributes->set('auth_username', $payload['unique_name']);
        $request->attributes->set('auth_role', $payload['role']);

        return $next($request);
    }
}
