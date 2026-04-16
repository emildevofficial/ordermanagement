<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Helper\Session;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * AuthMiddleware
 *
 * Sits in front of every protected route.
 * If the user has no active session → redirect to /login.
 * If the user IS logged in → attach their data to the request
 * and pass to the next handler.
 *
 * Flow:
 *   Request → AuthMiddleware → (passes) → DashboardHandler
 *                           → (blocks)  → RedirectResponse /login
 */
class AuthMiddleware implements MiddlewareInterface
{
    public function process(
        ServerRequestInterface  $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {

        Session::start();

        // No session = not logged in → block and redirect
        if (!Session::has('user_id')) {
            Session::flash('error', 'Please log in to continue.');
            return new RedirectResponse('/login');
        }

        // Pass user data to the handler via request attributes.
        // Handlers can then read: $request->getAttribute('user_id')
        $request = $request
            ->withAttribute('user_id',   Session::get('user_id'))
            ->withAttribute('user_name', Session::get('user_name'))
            ->withAttribute('user_role', Session::get('user_role'));

        // Pass to the next middleware or handler in the stack
        return $handler->handle($request);
    }
}