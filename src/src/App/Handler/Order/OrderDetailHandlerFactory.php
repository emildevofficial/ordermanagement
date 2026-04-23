<?php

declare(strict_types=1);

namespace App\Handler\Order;

use App\Database\Database;
use Psr\Container\ContainerInterface;
use Psr\Container\ContainerExceptionInterface;

final class OrderDetailHandlerFactory
{
    /**
     * @throws ContainerExceptionInterface
     */
    public function __invoke(ContainerInterface $container): OrderDetailHandler
    {
        return new OrderDetailHandler(
            $container->get(Database::class)
        );
    }
}

