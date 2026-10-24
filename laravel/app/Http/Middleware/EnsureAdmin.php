<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Area admin hanya untuk user is_admin. Selain itu pura-pura tidak ada (404), supaya URL admin tidak terbongkar. */
class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless($request->user()?->is_admin, 404);

        return $next($request);
    }
}
