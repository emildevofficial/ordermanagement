<?php

declare(strict_types=1);

namespace App\Handler\Product;

use App\Database\Database;
use Psr\Container\ContainerInterface;

class ProductListHandlerFactory
{
    public function __invoke(ContainerInterface $container): ProductListHandler
    {
        return new ProductListHandler(
            $container->get(Database::class)
        );
    }
}

