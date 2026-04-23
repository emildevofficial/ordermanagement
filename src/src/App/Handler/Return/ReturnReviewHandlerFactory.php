<?php

declare(strict_types=1);

namespace App\Handler\Return;

use App\Database\Database;
use Psr\Container\ContainerInterface;

class ReturnReviewHandlerFactory
{
    public function __invoke(ContainerInterface $container): ReturnReviewHandler
    {
        return new ReturnReviewHandler(
            $container->get(Database::class)
        );
    }
}
