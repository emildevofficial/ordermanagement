<?php

declare(strict_types=1);

namespace App\Handler\Customer;

use App\Database\Database;
use App\Helper\Session;
use App\Helper\Template;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CustomerDetailHandler implements RequestHandlerInterface
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        Session::start();

        $id = (int) $request->getAttribute('id');
        if ($id <= 0) {
            return new RedirectResponse('/customers');
        }

        $pdo = $this->db->getPdo();

        $customerStmt = $pdo->prepare("
            SELECT id, name, email, created_at
            FROM customers
            WHERE id = :id
            LIMIT 1
        ");
        $customerStmt->execute([':id' => $id]);
        $customer = $customerStmt->fetch();

        if (!$customer) {
            return new RedirectResponse('/customers');
        }

        // Admin-only: prevent non-admins from accessing customer detail
        $deny = \App\Helper\Permission::requireRole('admin');
        if ($deny instanceof \Laminas\Diactoros\Response\RedirectResponse) {
            return $deny;
        }

        $ordersStmt = $pdo->prepare("
            SELECT o.id, o.status, o.total, o.created_at
            FROM orders o
            WHERE o.customer_id = :customer_id
            ORDER BY o.created_at DESC
        ");
        $ordersStmt->execute([':customer_id' => $id]);
        $orders = $ordersStmt->fetchAll();

        // Fetch order items for each order
        $orderItems = [];
        foreach ($orders as $order) {
            $itemsStmt = $pdo->prepare("
                SELECT 
                    oi.quantity,
                    p.name as product_name,
                    p.price
                FROM order_items oi
                LEFT JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = :order_id
            ");
            $itemsStmt->execute([':order_id' => $order['id']]);
            $orderItems[$order['id']] = $itemsStmt->fetchAll();
        }

        $totalProductsOrdered = 0;
        foreach ($orderItems as $items) {
            foreach ($items as $item) {
                $totalProductsOrdered += (int)($item['quantity'] ?? 0);
            }
        }

        $totalAmountSpent = 0.0;
        foreach ($orders as $order) {
            $totalAmountSpent += (float)($order['total'] ?? 0);
        }

        $content = Template::render('customers/detail', [
            'customer' => $customer,
            'orders' => $orders,
            'orderItems' => $orderItems,
            'customerOrderSummary' => [
                'total_orders' => count($orders),
                'total_products_ordered' => $totalProductsOrdered,
                'total_amount_spent' => $totalAmountSpent,
                'latest_order_timestamp' => $orders[0]['created_at'] ?? null,
            ],
        ]);

        return new HtmlResponse(
            Template::render('layout', [
                'content' => $content,
                'currentRoute' => 'customers',
                'userName' => Session::get('user_name') ?? 'User',
                'role' => Session::get('user_role') ?? 'user',
            ])
        );
    }
}
