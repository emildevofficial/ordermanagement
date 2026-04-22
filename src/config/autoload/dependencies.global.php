<?php

declare(strict_types=1);

use App\Database\Database;
use App\Handler\Auth\LoginHandler;
use App\Handler\Auth\LoginHandlerFactory;
use App\Handler\Dashboard\DashboardHandler;
use App\Handler\Order\OrderListHandler;
use App\Handler\Order\OrderUpdateHandler;
use App\Handler\Order\OrderDeleteHandler;
use App\Handler\Order\OrderCreateHandler;



return [
    'dependencies' => [

        'aliases' => [
            // mund t’i lësh bosh për tani
        ],

        'invokables' => [
            // bosh
        ],

        'factories' => [

            // ✅ DATABASE (SHUMË E RËNDËSISHME)
            Database::class => function ($container) {
                $config = $container->get('config')['database'];
                return new Database($config);
            },

            // ✅ LOGIN HANDLER
            LoginHandler::class => LoginHandlerFactory::class,

            // ✅ DASHBOARD HANDLER (FIX I ERRORIT)
            DashboardHandler::class => function ($container) {
                return new DashboardHandler(
                    $container->get(Database::class)
                );
            },

            OrderListHandler::class => function ($container) {
    return new OrderListHandler(
        $container->get(Database::class)
    );
},

OrderUpdateHandler::class => function ($container) {
    return new OrderUpdateHandler(
        $container->get(\App\Database\Database::class)
    );
},

OrderDeleteHandler::class => function ($container) {
    return new OrderDeleteHandler(
        $container->get(\App\Database\Database::class)
    );
},

OrderCreateHandler::class => function ($c) {
    return new OrderCreateHandler(
        $c->get(\App\Database\Database::class)
    );
},

        ],
    ],
];