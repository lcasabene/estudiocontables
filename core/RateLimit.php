<?php

namespace Core;

class RateLimit
{
    public static function check(string $key, int $maxAttempts = 5, int $decayMinutes = 15): bool
    {
        $attempts = Session::get("rate_limit_{$key}", []);
        $now = time();
        $cutoff = $now - ($decayMinutes * 60);

        // Remove expired attempts
        $attempts = array_filter($attempts, fn($t) => $t > $cutoff);
        Session::set("rate_limit_{$key}", $attempts);

        return count($attempts) < $maxAttempts;
    }

    public static function hit(string $key): void
    {
        $attempts = Session::get("rate_limit_{$key}", []);
        $attempts[] = time();
        Session::set("rate_limit_{$key}", $attempts);
    }

    public static function clear(string $key): void
    {
        Session::remove("rate_limit_{$key}");
    }
}
