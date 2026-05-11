<?php

declare(strict_types=1);

namespace App\Handler\Order;

use App\Database\Database;
use Psr\Container\ContainerInterface;

final class OrderStatusActionHandlerFactory
{
    public function __invoke(ContainerInterface $container): OrderStatusActionHandler
    {
        return new OrderStatusActionHandler(
            $container->get(Database::class)
        );
    }
}
