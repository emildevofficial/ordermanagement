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

        $ordersStmt = $pdo->prepare("
            SELECT id, status, total, created_at
            FROM orders
            WHERE customer_id = :customer_id
            ORDER BY created_at DESC
        ");
        $ordersStmt->execute([':customer_id' => $id]);
        $orders = $ordersStmt->fetchAll();

        $content = Template::render('customers/detail', [
            'customer' => $customer,
            'orders' => $orders,
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
