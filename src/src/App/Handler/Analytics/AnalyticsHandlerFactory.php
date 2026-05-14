<?php

declare(strict_types=1);

namespace App\Handler\Analytics;

use App\Database\Database;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;

final class AnalyticsHandlerFactory
{
    /**
     * @throws ContainerExceptionInterface
     */
    public function __invoke(ContainerInterface $container): AnalyticsHandler
    {
        return new AnalyticsHandler(
            $container->get(Database::class)
        );
    }
}
