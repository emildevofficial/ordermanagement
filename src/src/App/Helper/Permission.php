<?php

declare(strict_types=1);

namespace App\Helper;

use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;

class Permission
{
    /**
     * Return the current user's role from session (defaults to 'user').
     */
    public static function getRole(): string
    {
        Session::start();
        return (string) (Session::get('user_role') ?? 'user');
    }

    /**
     * Check if current user has one of the allowed roles.
     * $allowed may be a string or an array of strings.
     */
    public static function isAllowed(array|string $allowed): bool
    {
        $role = self::getRole();
        $allowed = is_array($allowed) ? $allowed : [$allowed];
        return in_array($role, $allowed, true);
    }

    /**
     * Require one of the allowed roles — return null if allowed,
     * otherwise returns a RedirectResponse that callers should return.
     */
    public static function requireRole(array|string $allowed): ?ResponseInterface
    {
        if (self::isAllowed($allowed)) {
            return null;
        }

        Session::flash('error', 'You are not authorized to access that page.');
        return new RedirectResponse('/dashboard');
    }

    /**
     * Deny access if current user has any of the denied roles.
     * Useful to prevent admins accessing user-only pages.
     */
    public static function denyRole(array|string $denied): ?ResponseInterface
    {
        $denied = is_array($denied) ? $denied : [$denied];
        if (in_array(self::getRole(), $denied, true)) {
            Session::flash('error', 'You are not authorized to access that page.');
            return new RedirectResponse('/dashboard');
        }

        return null;
    }
}
