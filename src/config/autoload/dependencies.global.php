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
use App\Handler\Order\OrderDetailHandler;
use App\Handler\Customer\CustomerListHandler;
use App\Handler\Customer\CustomerListHandlerFactory;
use App\Handler\Customer\CustomerCreateHandler;
use App\Handler\Customer\CustomerDetailHandler;
use App\Handler\Customer\CustomerDetailHandlerFactory;



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
            \App\Handler\Dashboard\DashboardHandler::class => \App\Handler\Dashboard\DashboardHandlerFactory::class,

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

OrderDetailHandler::class => function ($c) {
    return new OrderDetailHandler(
        $c->get(\App\Database\Database::class)
    );
},

CustomerListHandler::class => CustomerListHandlerFactory::class,

CustomerCreateHandler::class => function ($container) {
    return new CustomerCreateHandler(
        $container->get(Database::class)
    );
},

CustomerDetailHandler::class => CustomerDetailHandlerFactory::class,

            // Product handlers

\App\Handler\Product\ProductListHandler::class => \App\Handler\Product\ProductListHandlerFactory::class,
\App\Handler\Product\ProductUpdateStockHandler::class => \App\Handler\Product\ProductUpdateStockHandlerFactory::class,
\App\Handler\Product\ProductToggleHandler::class => \App\Handler\Product\ProductToggleHandlerFactory::class,
\App\Handler\Product\ProductCreateHandler::class => \App\Handler\Product\ProductCreateHandlerFactory::class,

// Return handlers
\App\Handler\Return\ReturnListHandler::class => \App\Handler\Return\ReturnListHandlerFactory::class,
\App\Handler\Return\ReturnViewHandler::class => \App\Handler\Return\ReturnViewHandlerFactory::class,
\App\Handler\Return\ReturnReviewHandler::class => \App\Handler\Return\ReturnReviewHandlerFactory::class,
\App\Handler\Return\ReturnUpdateHandler::class => \App\Handler\Return\ReturnUpdateHandlerFactory::class,

\App\Helper\Template::class => function($container) {
    return new \App\Helper\Template();
},


        ],
    ],
]; 

