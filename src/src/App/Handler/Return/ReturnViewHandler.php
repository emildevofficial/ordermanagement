<?php

declare(strict_types=1);

namespace App\Handler\Return;

use App\Database\Database;
use App\Helper\Session;
use App\Helper\Template;
use App\Helper\Permission;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ReturnViewHandler implements RequestHandlerInterface
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        Session::start();
        $id = (int)$request->getAttribute('id');
        $currentRoute = 'returns';

        if ($id <= 0) {
            return new RedirectResponse('/returns');
        }

        $pdo = $this->db->getPdo();

        $stmt = $pdo->prepare("SELECT 
                r.*, 
                o.customer_id, 
                o.total AS order_total,
                o.created_at AS order_created_at,
                o.status AS order_status,
                o.user_id,
                u.name AS user_name,
                u.email AS user_email,
                c.name AS customer_name,
                c.email AS customer_email
            FROM returns r 
            JOIN orders o ON r.order_id = o.id 
            LEFT JOIN users u ON o.user_id = u.id
            LEFT JOIN customers c ON o.customer_id = c.id 
            WHERE r.id = :id");
        $stmt->execute([':id' => $id]);
        $return = $stmt->fetch();

        if (!$return) {
            return new RedirectResponse('/returns');
        }

        if (!Permission::isAllowed('admin') && (int)($return['user_id'] ?? 0) !== (int)Session::get('user_id')) {
            return new RedirectResponse('/returns');
        }

        // load order items for display
        $orderItems = [];
        try {
            $stmt = $pdo->prepare("SELECT oi.product_id, oi.quantity, p.name AS product_name
                FROM order_items oi
                LEFT JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = :oid");
            $stmt->execute([':oid' => $return['order_id']]);
            $orderItems = $stmt->fetchAll();
        } catch (\Throwable $e) {
            $orderItems = [];
        }

        $content = Template::render('return/view', [
            'return' => $return,
            'order' => [
                'id' => $return['order_id'],
                'total' => $return['order_total'] ?? 0,
                'created_at' => $return['order_created_at'] ?? null,
                'status' => $return['order_status'] ?? null,
                'customer_email' => $return['customer_email'] ?? null,
                'customer_id' => $return['customer_id'] ?? null,
                'customer_name' => $return['customer_name'] ?? null,
                'user_name' => $return['user_name'] ?? null,
                'user_email' => $return['user_email'] ?? null,
            ],
            'orderItems' => $orderItems,
        ]);

        return new HtmlResponse(
            Template::render('layout', [
                'content' => $content,
                'currentRoute' => $currentRoute,
                'userName' => Session::get('user_name'),
                'role' => Session::get('user_role'),
            ])
        );
    }
}
