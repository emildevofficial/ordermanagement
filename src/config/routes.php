<?php

declare(strict_types=1);

use App\Handler\Auth\LoginHandler;
use App\Handler\Auth\RegisterHandler;
use App\Handler\Auth\LogoutHandler;
use App\Handler\Dashboard\DashboardHandler;
use App\Handler\Order\OrderListHandler;
use App\Handler\Order\OrderCreateHandler;
use App\Handler\Order\OrderUpdateHandler;
use App\Handler\Order\OrderDeleteHandler;
use App\Handler\Order\OrderDetailHandler;
use App\Middleware\AuthMiddleware;
use Mezzio\Application;
use Mezzio\MiddlewareFactory; // 🔥 KJO MUNGONTE
use Psr\Container\ContainerInterface;
use App\Handler\Profile\ProfileHandler;
use App\Handler\Profile\EditProfileHandler;
use App\Handler\Profile\ChangePasswordHandler;
use App\Handler\Customer\CustomerListHandler;
use App\Handler\Settings\SettingsHandler;
use App\Handler\Customer\CustomerCreateHandler;
use App\Handler\Customer\CustomerDetailHandler;
use App\Middleware\RoleMiddleware;


return static function (
    Application $app,
    MiddlewareFactory $factory,   // 🔥 KJO MUNGONTE
    ContainerInterface $container
): void {

  
$app->get('/',        LoginHandler::class, 'home');     // ose direkt /login
$app->get('/login',   LoginHandler::class, 'login');    // 🔥 UI këtu

// ── POST actions ─────────────────────────────────
$app->post('/login',    LoginHandler::class,    'login.post');
$app->post('/register', RegisterHandler::class, 'register.post');


$app->get('/register', function () {
    return new \Laminas\Diactoros\Response\RedirectResponse('/login?tab=register');
});

// ── LOGOUT ───────────────────────────────────────
$app->get('/logout', LogoutHandler::class, 'logout');

    // ── Protected routes ───────────────────────────────────
    $app->get('/dashboard', [
        AuthMiddleware::class,
        DashboardHandler::class,
    ], 'dashboard');

    $app->get('/orders', [
        AuthMiddleware::class,
        OrderListHandler::class,
    ], 'orders.list');

   $app->get('/orders/create', [
    AuthMiddleware::class,
    OrderCreateHandler::class,
], 'orders.create');

$app->post('/orders/create', [
    AuthMiddleware::class,
    OrderCreateHandler::class,
], 'orders.create.post');

    $app->post('/orders/{id:\d+}/update', [
        AuthMiddleware::class,
        OrderUpdateHandler::class,
    ], 'orders.update');

    $app->post('/orders/{id:\d+}/delete', [
        AuthMiddleware::class,
        OrderDeleteHandler::class,
    ], 'orders.delete');

    $app->post('/orders/{id:\d+}/cancel', [
        AuthMiddleware::class,
        OrderDeleteHandler::class,
    ], 'orders.cancel');

    $app->get('/orders/{id:\d+}', [
        AuthMiddleware::class,
        OrderDetailHandler::class,
    ], 'orders.detail');


$app->get('/orders/{id:\d+}/edit', [
    AuthMiddleware::class,
    OrderUpdateHandler::class,
], 'orders.edit');

$app->post('/orders/{id:\d+}/edit', [
    AuthMiddleware::class,
    OrderUpdateHandler::class,
], 'orders.edit.post');

    $app->get('/profile', [
    AuthMiddleware::class,
    ProfileHandler::class,
], 'profile');

$app->get('/profile/edit', [
    AuthMiddleware::class,
    EditProfileHandler::class,
], 'profile.edit');

$app->post('/profile/edit', [
    AuthMiddleware::class,
    EditProfileHandler::class,
], 'profile.edit.post');

$app->get('/profile/password', [
    AuthMiddleware::class,
    ChangePasswordHandler::class,
]);

$app->post('/profile/password', [
    AuthMiddleware::class,
    ChangePasswordHandler::class,
]);

$app->get('/customers', CustomerListHandler::class, 'customers.list');

$app->route('/customers/create', [
    CustomerCreateHandler::class
], ['GET', 'POST'], 'customers.create');

$app->get('/customers/{id:\d+}', CustomerDetailHandler::class, 'customers.detail');


$app->get('/settings', [
    AuthMiddleware::class,
    SettingsHandler::class,
], 'settings');

// Product routes
$app->route('/products/create', [
    \App\Handler\Product\ProductCreateHandler::class
], ['GET','POST'], 'products.create');

$app->get('/products', [
    AuthMiddleware::class,
    \App\Handler\Product\ProductListHandler::class,
], 'products.list');

$app->post('/products/{id:\d+}/stock', [
    AuthMiddleware::class,
    \App\Handler\Product\ProductUpdateStockHandler::class,
], 'products.update-stock');

$app->post('/products/{id:\d+}/toggle', [
    AuthMiddleware::class,
    \App\Handler\Product\ProductToggleHandler::class,
], 'products.toggle');

// Return routes - admin only
$app->get('/returns', [
    \App\Middleware\AuthMiddleware::class,
    \App\Handler\Return\ReturnListHandler::class,
], 'returns.list');

$app->get('/returns/{id:\d+}', [
    AuthMiddleware::class,
    \App\Handler\Return\ReturnViewHandler::class,
], 'returns.view');

$app->get('/returns/{id:\d+}/review', [
    AuthMiddleware::class,
    \App\Handler\Return\ReturnReviewHandler::class,
], 'returns.review');

$app->post('/returns/{id:\d+}/update', [
    AuthMiddleware::class,
    \App\Handler\Return\ReturnUpdateHandler::class,
], 'returns.update');

};

