<?php

namespace App\Handler\Product;

use App\Database\Database;
use App\Helper\Template;
use Psr\Container\ContainerInterface;

class ProductCreateHandlerFactory
{
    public function __invoke(ContainerInterface $c)
    {
        return new ProductCreateHandler(
            $c->get(Database::class),
            $c->get(Template::class)
        );
    }
}
