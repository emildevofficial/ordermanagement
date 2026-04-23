<?php

declare(strict_types=1);

namespace App\Handler\Customer;

use App\Database\Database;
use Psr\Container\ContainerInterface;

class CustomerDetailHandlerFactory
{
    public function __invoke(ContainerInterface $container): CustomerDetailHandler
    {
        return new CustomerDetailHandler(
            $container->get(Database::class)
        );
    }
}
