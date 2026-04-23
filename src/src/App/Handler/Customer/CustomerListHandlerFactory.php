<?php

declare(strict_types=1);

namespace App\Handler\Customer;

use App\Database\Database;
use Psr\Container\ContainerInterface;

class CustomerListHandlerFactory
{
    public function __invoke(ContainerInterface $container): CustomerListHandler
    {
        return new CustomerListHandler(
            $container->get(Database::class)
        );
    }
}
