<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePlatformAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($request->session()->has('admin_original_user_id') || ! $user?->is_platform_admin) {
            abort(403, 'Área exclusiva del administrador de la plataforma.');
        }

        return $next($request);
    }
}
