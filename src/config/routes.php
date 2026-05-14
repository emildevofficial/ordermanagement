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
use App\Handler\Order\OrderStatusActionHandler;
use App\Middleware\AuthMiddleware;
use Mezzio\Application;
use Mezzio\MiddlewareFactory; 
use Psr\Container\ContainerInterface;
use App\Handler\Profile\ProfileHandler;
use App\Handler\Profile\EditProfileHandler;
use App\Handler\Profile\ChangePasswordHandler;
use App\Handler\Customer\CustomerListHandler;
use App\Handler\Return\ReturnCreateHandler;
use App\Handler\Settings\SettingsHandler;
use App\Handler\Customer\CustomerCreateHandler;
use App\Handler\Customer\CustomerDetailHandler;
use App\Middleware\RoleMiddleware;
use App\Handler\Product\InventoryToolsHandler;
use App\Handler\Product\ProductExportHandler;
use App\Handler\Product\ProductExportXlsxHandler;
use App\Handler\Product\ProductImportHandler;
use App\Handler\Product\ProductImportSampleHandler;
use App\Handler\Product\ProductBulkEditHandler;


return static function (
    Application $app,
    MiddlewareFactory $factory,   
    ContainerInterface $container
): void {

  
$app->get('/',        LoginHandler::class, 'home');     
$app->get('/login',   LoginHandler::class, 'login');    

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
        RoleMiddleware::class,
        DashboardHandler::class,
    ], 'dashboard');

    $app->get('/orders', [
        AuthMiddleware::class,
        OrderListHandler::class,
    ], 'orders.list');

    $app->get('/my-orders', [
        AuthMiddleware::class,
        OrderListHandler::class,
    ], 'orders.my-list');

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

    $app->post('/orders/{id:\d+}/status-action', [
        AuthMiddleware::class,
        OrderStatusActionHandler::class,
    ], 'orders.status-action');

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

$app->get('/customers', [
    AuthMiddleware::class,
    RoleMiddleware::class,
    CustomerListHandler::class,
], 'customers.list');

$app->route('/customers/create', [
    AuthMiddleware::class,
    RoleMiddleware::class,
    CustomerCreateHandler::class
], ['GET', 'POST'], 'customers.create');

$app->get('/customers/{id:\d+}', [
    AuthMiddleware::class,
    RoleMiddleware::class,
    CustomerDetailHandler::class,
], 'customers.detail');


$app->get('/settings', [
    AuthMiddleware::class,
    SettingsHandler::class,
], 'settings');

$app->post('/settings', [
    AuthMiddleware::class,
    SettingsHandler::class,
], 'settings.post');

// Product routes
$app->get('/inventory-tools', function () {
    return new \Laminas\Diactoros\Response\RedirectResponse('/import-export');
}, 'inventory-tools.redirect');

$app->get('/import-export', [
    AuthMiddleware::class,
    RoleMiddleware::class,
    InventoryToolsHandler::class,
], 'import-export');

$app->get('/import-export/export', [
    AuthMiddleware::class,
    RoleMiddleware::class,
    ProductExportHandler::class,
], 'import-export.export');

$app->get('/import-export/export-xlsx', [
    AuthMiddleware::class,
    RoleMiddleware::class,
    ProductExportXlsxHandler::class,
], 'import-export.export-xlsx');

$app->get('/import-export/sample', [
    AuthMiddleware::class,
    RoleMiddleware::class,
    ProductImportSampleHandler::class,
], 'import-export.sample');

$app->post('/import-export/import', [
    AuthMiddleware::class,
    RoleMiddleware::class,
    ProductImportHandler::class,
], 'import-export.import');

$app->post('/import-export/bulk-edit', [
    AuthMiddleware::class,
    RoleMiddleware::class,
    ProductBulkEditHandler::class,
], 'import-export.bulk-edit');

$app->route('/products/create', [
    AuthMiddleware::class,
    \App\Handler\Product\ProductCreateHandler::class
], ['GET','POST'], 'products.create');

$app->get('/products', [
    AuthMiddleware::class,
    \App\Handler\Product\ProductListHandler::class,
], 'products.list');

$app->get('/shop', [
    AuthMiddleware::class,
    \App\Handler\Product\ProductListHandler::class,
], 'shop');

$app->post('/products/{id:\d+}/stock', [
    AuthMiddleware::class,
    \App\Handler\Product\ProductUpdateStockHandler::class,
], 'products.update-stock');

$app->post('/products/{id:\d+}/toggle', [
    AuthMiddleware::class,
    \App\Handler\Product\ProductToggleHandler::class,
], 'products.toggle');

// Edit product (admin only) - show form and save
$app->get('/products/{id:\d+}/edit', [
    AuthMiddleware::class,
    \App\Handler\Product\ProductEditHandler::class,
], 'products.edit');

$app->post('/products/{id:\d+}/edit', [
    AuthMiddleware::class,
    \App\Handler\Product\ProductEditHandler::class,
], 'products.edit.post');

// Return routes - admin only
$app->get('/returns', [
    \App\Middleware\AuthMiddleware::class,
    \App\Handler\Return\ReturnListHandler::class,
], 'returns.list');

$app->route('/returns/create', [
    AuthMiddleware::class,
    ReturnCreateHandler::class,
], ['GET','POST'], 'returns.create');

$app->get('/returns/{id:\d+}', [
    AuthMiddleware::class,
    \App\Handler\Return\ReturnViewHandler::class,
], 'returns.view');

$app->post('/returns/{id:\d+}/update', [
    AuthMiddleware::class,
    \App\Handler\Return\ReturnUpdateHandler::class,
], 'returns.update');

};

