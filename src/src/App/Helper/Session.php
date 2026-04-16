<?php

declare(strict_types=1);

namespace App\Helper;

/**
 * Session — a simple wrapper around PHP's native $_SESSION.
 *
 * Why not use $_SESSION directly?
 * Because if you call session_start() in 5 different places,
 * you get errors. This class ensures it's started once and
 * gives you clean methods to use everywhere.
 */
class Session
{
    /**
     * Start the session if not already started.
     * Call this once at the top of any handler that needs session.
     */
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Store a value in the session.
     * Example: Session::set('user_id', 5);
     */
    public static function set(string $key, mixed $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    /**
     * Get a value from the session.
     * Example: Session::get('user_id') → returns 5
     * Returns $default if key doesn't exist.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Check if a key exists in the session.
     * Example: Session::has('user_id') → true or false
     */
    public static function has(string $key): bool
    {
        self::start();
        return isset($_SESSION[$key]);
    }

    /**
     * Remove one key from the session.
     * Example: Session::remove('flash_message');
     */
    public static function remove(string $key): void
    {
        self::start();
        unset($_SESSION[$key]);
    }

    /**
     * Destroy the entire session (used in logout).
     * Clears all session data and the session cookie.
     */
    public static function destroy(): void
    {
        self::start();
        $_SESSION = [];
        session_unset();
        session_destroy();
    }

    /**
     * Flash messages — show once then disappear.
     * Used to show "Login failed" or "Registered successfully".
     *
     * Set:  Session::flash('error', 'Wrong password')
     * Get:  Session::getFlash('error')  → 'Wrong password' (then it's deleted)
     */
    public static function flash(string $key, string $message): void
    {
        self::set('flash_' . $key, $message);
    }

    public static function getFlash(string $key): ?string
    {
       $val = self::get('flash_' . $key);
        self::remove('flash_' . $key);
        return $val;
    }
}