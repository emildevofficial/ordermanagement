<?php

declare(strict_types=1);

namespace App\Handler\Product;

use App\Database\Database;
use Psr\Container\ContainerInterface;

class ProductUpdateStockHandlerFactory
{
    public function __invoke(ContainerInterface $container): ProductUpdateStockHandler
    {
        return new ProductUpdateStockHandler(
            $container->get(Database::class)
        );
    }
}

