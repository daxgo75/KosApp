<?php

namespace App\Exceptions;

use App\Services\LogService;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            $this->logException($e);
        });

        $this->renderable(function (Throwable $e, $request) {
            return $this->handleException($e, $request);
        });
    }

    private function logException(Throwable $e): void
    {
        if ($e instanceof ValidationException) {
            LogService::warning('Validation error', [
                'errors' => $e->errors(),
                'url' => request()->url(),
            ]);
        } elseif ($e instanceof AuthenticationException) {
            LogService::logSecurityEvent('Authentication failed', [
                'path' => request()->path(),
            ]);
        } elseif ($e instanceof QueryException) {
            LogService::logDatabaseError($e);
        } else {
            LogService::error('Unexpected error: ' . class_basename($e), [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'url' => request()->url(),
                'method' => request()->method(),
            ]);
        }
    }

    private function handleException(Throwable $e, $request)
    {
        if ($request->expectsJson()) {
            return $this->jsonResponse($e);
        }

        return null;
    }

    private function jsonResponse(Throwable $e)
    {
        $status = 500;
        $message = 'An error occurred';

        if ($e instanceof ValidationException) {
            $status = 422;
            $message = 'Validation failed';

            return response()->json([
                'success' => false,
                'message' => $message,
                'errors' => $e->errors(),
            ], $status);
        }

        if ($e instanceof AuthenticationException) {
            $status = 401;
            $message = 'Unauthenticated';
        }

        if ($e instanceof NotFoundHttpException) {
            $status = 404;
            $message = 'Resource not found';
        }

        if (config('app.debug')) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'exception' => class_basename($e),
                'error' => $e->getMessage(),
                'trace' => $e->getTrace(),
            ], $status);
        }

        return response()->json([
            'success' => false,
            'message' => $message,
        ], $status);
    }
}
