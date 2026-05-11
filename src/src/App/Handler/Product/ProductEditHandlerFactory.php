<?php

declare(strict_types=1);

namespace App\Handler\Product;

use App\Database\Database;
use App\Helper\Template;
use Psr\Container\ContainerInterface;

class ProductEditHandlerFactory
{
    public function __invoke(ContainerInterface $container): ProductEditHandler
    {
        return new ProductEditHandler(
            $container->get(Database::class),
            $container->get(Template::class)
        );
    }
}
