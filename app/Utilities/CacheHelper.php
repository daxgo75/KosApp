<?php

namespace App\Utilities;

use Illuminate\Support\Facades\Cache;

class CacheHelper
{
    public const CACHE_TTL = 3600; // 1 hour
    public const CACHE_TTL_SHORT = 300; // 5 minutes
    public const CACHE_TTL_LONG = 86400; // 1 day

    public static function remember(string $key, callable $callback, int $ttl = self::CACHE_TTL)
    {
        return Cache::remember($key, $ttl, $callback);
    }

    public static function rememberForever(string $key, callable $callback)
    {
        return Cache::rememberForever($key, $callback);
    }

    public static function get(string $key, $default = null)
    {
        return Cache::get($key, $default);
    }

    public static function put(string $key, $value, int $ttl = self::CACHE_TTL)
    {
        return Cache::put($key, $value, $ttl);
    }

    public static function forget(string $key)
    {
        return Cache::forget($key);
    }

    public static function flush()
    {
        return Cache::flush();
    }

    public static function key(string ...$parts): string
    {
        return implode(':', $parts);
    }

    public static function invalidatePattern(string $pattern)
    {
        // For file cache, this needs to be implemented with a cache driver
        // that supports pattern deletion like Redis
        return Cache::flush();
    }
}
