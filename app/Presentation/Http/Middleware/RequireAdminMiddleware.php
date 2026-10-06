<?php

declare(strict_types=1);

namespace App\Presentation\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class RequireAdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $role = $request->attributes->get('auth_role');
        if ($role !== 'admin') {
            return response('', 403);
        }

        return $next($request);
    }
}
