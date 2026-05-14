<?php

declare(strict_types=1);

namespace App\Handler\Order;

use App\Database\Database;
use App\Helper\DateTimeHelper;
use App\Helper\Session;
use App\Helper\Permission;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class OrderDeleteHandler implements RequestHandlerInterface
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
        $role = Session::get('user_role') ?? 'user';
        $userId = (int) (Session::get('user_id') ?? 0);

        $pdo = $this->db->getPdo();
        $updatedAt = DateTimeHelper::nowForStorage();

        try {
            $pdo->beginTransaction();

            if (Permission::isAllowed('admin')) {
                $stmt = $pdo->prepare("SELECT id, status FROM orders WHERE id = :id FOR UPDATE");
                $stmt->execute([':id' => $id]);
            } else {
                $stmt = $pdo->prepare("SELECT id, status FROM orders WHERE id = :id AND user_id = :user_id FOR UPDATE");
                $stmt->execute([
                    ':id' => $id,
                    ':user_id' => $userId,
                ]);
            }

            $order = $stmt->fetch();
            if (!$order || (string)$order['status'] !== 'pending') {
                $pdo->rollBack();
                return new RedirectResponse('/orders');
            }

            $stmt = $pdo->prepare("
                UPDATE orders
                SET status = :status, updated_at = :updated_at
                WHERE id = :id AND status = 'pending'
            ");
            $stmt->execute([
                ':status' => 'cancelled',
                ':updated_at' => $updatedAt,
                ':id' => $id,
            ]);

            if ($stmt->rowCount() === 1) {
                $stmt = $pdo->prepare("SELECT product_id, quantity FROM order_items WHERE order_id = :order_id");
                $stmt->execute([':order_id' => $id]);
                $items = $stmt->fetchAll();

                foreach ($items as $item) {
                    $stmt = $pdo->prepare("UPDATE products SET stock = stock + :quantity WHERE id = :product_id");
                    $stmt->execute([
                        ':quantity' => (int)$item['quantity'],
                        ':product_id' => (int)$item['product_id'],
                    ]);
                }
            }

            $pdo->commit();
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
        }

        return new RedirectResponse('/orders');
    }
}
