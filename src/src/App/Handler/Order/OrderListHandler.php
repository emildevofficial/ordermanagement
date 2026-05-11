<?php

declare(strict_types=1);

namespace App\Handler\Order;

use App\Database\Database;
use App\Helper\Session;
use App\Helper\Permission;
use App\Helper\Template;
use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class OrderListHandler implements RequestHandlerInterface
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        Session::start();

        $userId = Session::get('user_id') ?? null;
        $role   = Session::get('user_role') ?? 'user';
        $userName = Session::get('user_name') ?? 'User';

        $currentRoute = 'orders';

        $pdo = $this->db->getPdo();

        if (Permission::isAllowed('admin')) {
            // Admin sees all orders with customer information
            $stmt = $pdo->query("
                SELECT 
                    o.id,
                    o.status,
                    o.total,
                    o.created_at,
                    COALESCE(c.name, u.name, 'Unknown Customer') AS customer_name,
                    COALESCE(c.email, u.email, '') AS customer_email,
                    p.name AS product_name,
                    oi.quantity,
                    EXISTS(SELECT 1 FROM returns r WHERE r.order_id = o.id) AS has_return
                FROM orders o
                LEFT JOIN users u ON u.id = o.user_id
                LEFT JOIN customers c ON o.customer_id = c.id
                LEFT JOIN order_items oi ON oi.order_id = o.id
                LEFT JOIN products p ON p.id = oi.product_id
                ORDER BY o.created_at DESC
            ");
            $orders = $stmt->fetchAll();
        } else {
            // Regular user sees only their own orders with their user information
            $stmt = $pdo->prepare("
                SELECT 
                    o.id,
                    o.status,
                    o.total,
                    o.created_at,
                    u.name AS customer_name,
                    u.email AS customer_email,
                    p.name AS product_name,
                    oi.quantity,
                    EXISTS(SELECT 1 FROM returns r WHERE r.order_id = o.id) AS has_return
                FROM orders o
                JOIN users u ON o.user_id = u.id
                LEFT JOIN order_items oi ON oi.order_id = o.id
                LEFT JOIN products p ON p.id = oi.product_id
                WHERE o.user_id = :user_id
                ORDER BY o.created_at DESC
            ");
            $stmt->execute([':user_id' => $userId]);
            $orders = $stmt->fetchAll();
        }

        $isAdmin = Permission::isAllowed('admin');
        foreach ($orders as &$order) {
            $status = (string)($order['status'] ?? '');
            $hasReturn = !empty($order['has_return']);

            $order['can_cancel'] = $status === 'pending';
            $order['can_return'] = !$isAdmin
                && in_array($status, ['completed', 'delivered'], true)
                && !$hasReturn;
        }
        unset($order);

        $orderCount = count($orders);

        $content = Template::render('order/list', [
            'orders' => $orders,
            'orderCount' => $orderCount,
        ]);

        return new HtmlResponse(
            Template::render('layout', [
                'content' => $content,
                'currentRoute' => $currentRoute,
                'userName' => $userName,
                'role' => $role,
            ])
        );
    }
}
