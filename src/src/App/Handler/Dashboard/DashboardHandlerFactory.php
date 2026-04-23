<?php

declare(strict_types=1);

namespace App\Handler\Dashboard;

use App\Database\Database;
use Psr\Container\ContainerInterface;
use Psr\Container\ContainerExceptionInterface;

final class DashboardHandlerFactory
{
    /**
     * @throws ContainerExceptionInterface
     */
    public function __invoke(ContainerInterface $container): DashboardHandler
    {
        return new DashboardHandler(
            $container->get(Database::class)
        );
    }
}

