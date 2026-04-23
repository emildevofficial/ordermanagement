<?php

declare(strict_types=1);

namespace App\Handler\Return;

use App\Database\Database;
use Psr\Container\ContainerInterface;

class ReturnListHandlerFactory
{
    public function __invoke(ContainerInterface $container): ReturnListHandler
    {
        return new ReturnListHandler(
            $container->get(Database::class)
        );
    }
}
