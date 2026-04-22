<?php

declare(strict_types=1);

namespace App\Handler\Order;

use App\Database\Database;
use App\Helper\Session;
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

        if ($role === 'admin') {
            $stmt = $pdo->query("
                SELECT
                    o.*,
                    c.name AS customer_name,
                    c.email AS customer_email
                FROM orders o
                JOIN customers c ON c.id = o.customer_id
                ORDER BY created_at DESC
            ");
        } else {
            $stmt = $pdo->prepare("
                SELECT
                    o.*,
                    c.name AS customer_name,
                    c.email AS customer_email
                FROM orders o
                JOIN customers c ON c.id = o.customer_id
                WHERE user_id = :user_id
                ORDER BY created_at DESC
            ");
            $stmt->execute([':user_id' => $userId]);
        }

        $orders = $stmt->fetchAll();
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