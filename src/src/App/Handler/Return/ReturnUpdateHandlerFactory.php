<?php

declare(strict_types=1);

namespace App\Handler\Return;

use App\Database\Database;
use Psr\Container\ContainerInterface;

class ReturnUpdateHandlerFactory
{
    public function __invoke(ContainerInterface $container): ReturnUpdateHandler
    {
        return new ReturnUpdateHandler(
            $container->get(Database::class)
        );
    }
}
