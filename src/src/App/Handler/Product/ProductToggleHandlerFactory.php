<?php

declare(strict_types=1);

namespace App\Handler\Product;

use App\Database\Database;
use Psr\Container\ContainerInterface;

class ProductToggleHandlerFactory
{
    public function __invoke(ContainerInterface $container): ProductToggleHandler
    {
        return new ProductToggleHandler(
            $container->get(Database::class)
        );
    }
}

