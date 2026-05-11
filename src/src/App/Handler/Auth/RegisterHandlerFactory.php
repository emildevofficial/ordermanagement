<?php

declare(strict_types=1);

namespace App\Handler\Auth;

use App\Database\Database;
use Psr\Container\ContainerInterface;

class RegisterHandlerFactory
{
    public function __invoke(ContainerInterface $container): RegisterHandler
    {
        return new RegisterHandler(
            $container->get(Database::class)
        );
    }
}
