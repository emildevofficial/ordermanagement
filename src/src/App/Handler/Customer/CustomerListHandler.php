<?php

declare(strict_types=1);

namespace App\Handler\Customer;

use App\Database\Database;
use App\Helper\Session;
use App\Helper\Permission;
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

        // Admin-only: prevent non-admins from accessing customers listing
        $deny = Permission::requireRole('admin');
        if ($deny instanceof \Laminas\Diactoros\Response\RedirectResponse) {
            return $deny;
        }

        $pdo = $this->db->getPdo();
        $stmt = $pdo->prepare("
            SELECT
                COALESCE(c.id, u.id) AS id,
                COALESCE(c.name, u.name) AS name,
                COALESCE(c.email, u.email) AS email,
                COALESCE(c.created_at, u.created_at) AS created_at,
                COUNT(o.id) AS order_count,
                COALESCE(SUM(o.total), 0) AS total_spent
            FROM users u
            LEFT JOIN customers c ON c.user_id = u.id
            LEFT JOIN orders o ON o.user_id = u.id
            WHERE u.role = 'user'
            GROUP BY u.id, u.name, u.email, u.created_at, c.id, c.name, c.email, c.created_at
            ORDER BY COALESCE(c.created_at, u.created_at) DESC
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
