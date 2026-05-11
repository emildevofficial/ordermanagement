<?php

declare(strict_types=1);

namespace App\Handler\Order;

use App\Database\Database;
use App\Helper\DateTimeHelper;
use App\Helper\Permission;
use App\Helper\Session;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class OrderStatusActionHandler implements RequestHandlerInterface
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        Session::start();

        if (!Permission::isAllowed('admin')) {
            return new JsonResponse(['success' => false, 'error' => 'Forbidden'], 403);
        }

        $id = (int)$request->getAttribute('id');
        $data = $request->getParsedBody();
        $action = (string)($data['action'] ?? '');

        if ($id <= 0 || !in_array($action, ['complete', 'cancel'], true)) {
            return new JsonResponse(['success' => false, 'error' => 'Invalid request'], 400);
        }

        $pdo = $this->db->getPdo();

        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("SELECT id, status FROM orders WHERE id = :id FOR UPDATE");
            $stmt->execute([':id' => $id]);
            $order = $stmt->fetch();

            if (!$order) {
                $pdo->rollBack();
                return new JsonResponse(['success' => false, 'error' => 'Order not found'], 404);
            }

            if ((string)$order['status'] !== 'pending') {
                $pdo->rollBack();
                return new JsonResponse(['success' => false, 'error' => 'Only pending orders can be updated'], 409);
            }

            $newStatus = $action === 'complete' ? 'completed' : 'cancelled';

            $stmt = $pdo->prepare("UPDATE orders SET status = :status, updated_at = :updated_at WHERE id = :id");
            $stmt->execute([
                ':status' => $newStatus,
                ':updated_at' => DateTimeHelper::nowForStorage(),
                ':id' => $id,
            ]);

            if ($action === 'cancel') {
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

            return new JsonResponse(['success' => true, 'status' => $newStatus]);
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            error_log('Order status action failed: ' . $e->getMessage());

            return new JsonResponse(['success' => false, 'error' => 'Could not update order status'], 500);
        }
    }
}
