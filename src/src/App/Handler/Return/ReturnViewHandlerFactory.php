<?php

declare(strict_types=1);

namespace App\Handler\Return;

use App\Database\Database;
use Psr\Container\ContainerInterface;

class ReturnViewHandlerFactory
{
    public function __invoke(ContainerInterface $container): ReturnViewHandler
    {
        return new ReturnViewHandler(
            $container->get(Database::class)
        );
    }
}
