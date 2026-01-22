<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateJsonInput
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isJson() && $request->getContent()) {
            try {
                json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid JSON input',
                    'error' => $e->getMessage(),
                ], 400);
            }
        }

        return $next($request);
    }
}
