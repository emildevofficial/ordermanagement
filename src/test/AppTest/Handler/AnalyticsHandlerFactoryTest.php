<?php

declare(strict_types=1);

namespace AppTest\Handler;

use App\Database\Database;
use App\Handler\Analytics\AnalyticsHandler;
use App\Handler\Analytics\AnalyticsHandlerFactory;
use AppTest\InMemoryContainer;
use PHPUnit\Framework\TestCase;

final class AnalyticsHandlerFactoryTest extends TestCase
{
    public function testFactoryCreatesAnalyticsHandler(): void
    {
        $container = new InMemoryContainer();
        $container->setService(Database::class, $this->createMock(Database::class));

        $factory = new AnalyticsHandlerFactory();

        self::assertInstanceOf(AnalyticsHandler::class, $factory($container));
    }
}
