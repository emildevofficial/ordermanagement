<?php

declare(strict_types=1);

namespace App\Handler\Return;

use App\Database\Database;
use App\Helper\DateTimeHelper;
use App\Helper\Session;
use App\Helper\Template;
use App\Helper\Permission;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ReturnCreateHandler implements RequestHandlerInterface
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        Session::start();

        if (!Session::get('user_id')) {
            return new RedirectResponse('/login');
        }

        // Admins should not create user return requests here
        if (Permission::isAllowed('admin')) {
            return new RedirectResponse('/returns');
        }

        $pdo = $this->db->getPdo();
        $this->ensureReturnOwnershipColumns($pdo);

        $userId = (int) Session::get('user_id');
        $currentRoute = 'returns';

        // order_id may come via GET (link) or POST (form)
        $orderId = (int) ($request->getQueryParams()['order_id'] ?? $request->getParsedBody()['order_id'] ?? 0);

        if ($orderId <= 0) {
            return new RedirectResponse('/orders');
        }

        // Ensure order exists and belongs to the current user.
        $stmt = $pdo->prepare("SELECT id, user_id, customer_id, status FROM orders WHERE id = :id");
        $stmt->execute([':id' => $orderId]);
        $order = $stmt->fetch();

        if (!$order || (int)$order['user_id'] !== $userId) {
            return new RedirectResponse('/orders');
        }

        $customerId = $order['customer_id'] ?? null;
        if ($customerId === null || $customerId === '') {
            $stmt = $pdo->prepare('SELECT id FROM customers WHERE user_id = :user_id LIMIT 1');
            $stmt->execute([':user_id' => $userId]);
            $customer = $stmt->fetch();
            $customerId = $customer['id'] ?? null;
        }

        // Check if return already exists for this order
        $stmt = $pdo->prepare("SELECT id FROM returns WHERE order_id = :order_id");
        $stmt->execute([':order_id' => $orderId]);
        if ($stmt->fetch()) {
            // Return already exists, redirect to returns list
            return new RedirectResponse('/returns');
        }

        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();
            $reason = trim((string) ($data['reason'] ?? ''));

            if ($reason === '') {
                $content = Template::render('return/create', [
                    'order' => $order,
                    'error' => 'Reason is required.',
                    'reason' => $reason,
                ]);
                return new HtmlResponse(Template::render('layout', [
                    'content' => $content,
                    'currentRoute' => $currentRoute,
                    'userName' => Session::get('user_name'),
                    'role' => Session::get('user_role'),
                ]));
            }

            try {
                $stmt = $pdo->prepare("
                    INSERT INTO returns (order_id, user_id, customer_id, reason, status, created_at)
                    VALUES (:order_id, :user_id, :customer_id, :reason, 'pending', :created_at)
                ");
                $stmt->execute([
                    ':order_id' => $orderId,
                    ':user_id' => $userId,
                    ':customer_id' => $customerId,
                    ':reason' => $reason,
                    ':created_at' => DateTimeHelper::nowForStorage(),
                ]);

                return new RedirectResponse('/returns');
            } catch (\PDOException $e) {
                if ($e->getCode() === '23000') {
                    return new RedirectResponse('/returns');
                }

                $content = Template::render('return/create', [
                    'order' => $order,
                    'error' => 'Failed to create return: ' . $e->getMessage(),
                    'reason' => $reason,
                ]);
                return new HtmlResponse(Template::render('layout', [
                    'content' => $content,
                    'currentRoute' => $currentRoute,
                    'userName' => Session::get('user_name'),
                    'role' => Session::get('user_role'),
                ]));
            } catch (\Exception $e) {
                $content = Template::render('return/create', [
                    'order' => $order,
                    'error' => 'Failed to create return: ' . $e->getMessage(),
                    'reason' => $reason,
                ]);
                return new HtmlResponse(Template::render('layout', [
                    'content' => $content,
                    'currentRoute' => $currentRoute,
                    'userName' => Session::get('user_name'),
                    'role' => Session::get('user_role'),
                ]));
            }
        }

        $content = Template::render('return/create', [
            'order' => $order,
        ]);

        return new HtmlResponse(Template::render('layout', [
            'content' => $content,
            'currentRoute' => $currentRoute,
            'userName' => Session::get('user_name'),
            'role' => Session::get('user_role'),
        ]));
    }

    private function ensureReturnOwnershipColumns(\PDO $pdo): void
    {
        $columns = [
            'user_id' => 'ALTER TABLE returns ADD COLUMN user_id INT UNSIGNED NULL AFTER order_id',
            'customer_id' => 'ALTER TABLE returns ADD COLUMN customer_id INT UNSIGNED NULL AFTER user_id',
        ];

        foreach ($columns as $column => $sql) {
            $stmt = $pdo->prepare('SHOW COLUMNS FROM returns LIKE :column');
            $stmt->execute([':column' => $column]);

            if (!$stmt->fetch()) {
                $pdo->exec($sql);
            }
        }
    }
}
