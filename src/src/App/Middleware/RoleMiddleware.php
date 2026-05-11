<?php

declare(strict_types=1);

namespace App\Middleware;

use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use App\Helper\Permission;

class RoleMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Prefer explicit role attribute attached by AuthMiddleware,
        // fall back to Permission helper which reads session.
        $role = $request->getAttribute('user_role') ?? Permission::getRole();

        if ($role !== 'admin') {
            return new RedirectResponse('/shop');
        }

        return $handler->handle($request);
    }
}
