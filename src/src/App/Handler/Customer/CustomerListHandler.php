<?php

declare(strict_types=1);

namespace App\Handler\Customer;

use App\Database\Database;
use App\Helper\Session;
use App\Helper\Template;
use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CustomerListHandler implements RequestHandlerInterface
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        Session::start();

        $userName = Session::get('user_name') ?? 'User';
        $role = Session::get('user_role') ?? 'user';
        $currentRoute = 'customers';

        $pdo = $this->db->getPdo();

        $stmt = $pdo->prepare("
            SELECT
                c.id,
                c.name,
                c.email,
                c.created_at,
                COUNT(o.id) AS order_count,
                COALESCE(SUM(o.total), 0) AS total_spent
            FROM customers c
            LEFT JOIN orders o ON o.customer_id = c.id
            GROUP BY c.id, c.name, c.email, c.created_at
            ORDER BY c.created_at DESC
        ");
        $stmt->execute();

        $customers = $stmt->fetchAll();

        $content = Template::render('customers/list', [
            'customers' => $customers,
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
