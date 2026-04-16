<?php

declare(strict_types=1);

namespace App\Handler\Auth;

use App\Helper\Session;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class LogoutHandler implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // Ensure session is started
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        // Destroy all session data
        $_SESSION = [];

        // Destroy session completely
        session_destroy();

        // Delete session cookie from browser
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        // Optional flash message (if your Session helper supports it)
        Session::flash('success', 'You have been logged out.');

        // Redirect to your auth page (NOT /login anymore)
        return new RedirectResponse('/register');
    }
}