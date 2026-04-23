<?php

declare(strict_types=1);

namespace App\Handler\Order;

use App\Database\Database;
use App\Helper\Session;
use App\Helper\Template;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class OrderDetailHandler implements RequestHandlerInterface
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
        $role = Session::get('user_role') ?? 'user';
        $userName = Session::get('user_name') ?? 'User';
        $currentRoute = 'orders';

        $id = (int) $request->getAttribute('id');
        if ($id <= 0) {
            return new RedirectResponse('/orders');
        }

        $pdo = $this->db->getPdo();

        // Fetch order with customer info
        $stmt = $pdo->prepare("
            SELECT 
                o.*,
                c.name as customer_name,
                c.email as customer_email
            FROM orders o
            LEFT JOIN customers c ON c.id = o.customer_id
            WHERE o.id = :id
        ");
        $stmt->execute([':id' => $id]);
        $order = $stmt->fetch();

        if (!$order) {
            return new RedirectResponse('/orders');
        }

        // Authorization check
        if ($role !== 'admin' && $order['user_id'] != $userId) {
            return new RedirectResponse('/orders');
        }

        $content = Template::render('order/detail', [
            'order' => $order,
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

