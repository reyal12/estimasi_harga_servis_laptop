<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class EnsureSessionToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->cookie('estimasi_session_token');

        if (! $token || ! preg_match('/^[0-9a-f-]{36}$/', $token)) {
            $token = (string) Str::uuid();
        }

        $request->attributes->set('session_token', $token);

        $response = $next($request);

        return $response->cookie(
            'estimasi_session_token',
            $token,
            60 * 24 * 365, // 1 year
            '/',
            null,
            false,
            true  // httpOnly
        );
    }
}
