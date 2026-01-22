<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class LogService
{
    private const LOG_CHANNEL = 'application';

    public static function info(string $message, array $context = []): void
    {
        Log::channel(self::LOG_CHANNEL)->info($message, $context);
    }

    public static function warning(string $message, array $context = []): void
    {
        Log::channel(self::LOG_CHANNEL)->warning($message, $context);
    }

    public static function error(string $message, array $context = []): void
    {
        Log::channel(self::LOG_CHANNEL)->error($message, $context);
    }

    public static function critical(string $message, array $context = []): void
    {
        Log::channel(self::LOG_CHANNEL)->critical($message, $context);
    }

    public static function logAction(string $action, string $model, ?int $modelId = null, ?string $details = null, ?int $userId = null): void
    {
        Log::channel('activity')->info($action, [
            'model' => $model,
            'model_id' => $modelId,
            'user_id' => $userId ?? optional(auth('sanctum')->user())->id,
            'details' => $details,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now(),
        ]);
    }

    public static function logPaymentAction(string $action, int $paymentId, ?int $userId = null, ?string $details = null): void
    {
        Log::channel('payment')->info($action, [
            'payment_id' => $paymentId,
            'user_id' => $userId ?? optional(auth('sanctum')->user())->id,
            'details' => $details,
            'timestamp' => now(),
        ]);
    }

    public static function logSecurityEvent(string $event, array $context = []): void
    {
        Log::channel('security')->warning($event, array_merge([
            'user_id' => optional(auth('sanctum')->user())->id,
            'ip_address' => request()->ip(),
            'timestamp' => now(),
        ], $context));
    }

    public static function logDatabaseError(\Throwable $exception, array $context = []): void
    {
        Log::channel('database')->error('Database error', array_merge([
            'exception' => class_basename($exception),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
        ], $context));
    }
}
