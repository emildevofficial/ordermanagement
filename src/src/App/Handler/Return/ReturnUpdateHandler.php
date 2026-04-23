<?php

declare(strict_types=1);

namespace App\Handler\Return;

use App\Database\Database;
use App\Helper\Session;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ReturnUpdateHandler implements RequestHandlerInterface
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
        $data = $request->getParsedBody();
        $action = $data['action'] ?? '';
        $notes = trim($data['notes'] ?? '');

        if ($id <= 0 || !in_array($action, ['approve', 'reject'])) {
            return new RedirectResponse('/returns');
        }

        $pdo = $this->db->getPdo();

        try {
            $pdo->beginTransaction();

            // Verify return exists and status is requested
            $stmt = $pdo->prepare("SELECT order_id, status FROM returns WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $return = $stmt->fetch();

            if (!$return || $return['status'] !== 'requested') {
                $pdo->rollBack();
                return new RedirectResponse('/returns');
            }

            $newStatus = $action === 'approve' ? 'approved' : 'rejected';

            // Update return status + notes
            $stmt = $pdo->prepare("
                UPDATE returns 
                SET status = :status, notes = :notes
                WHERE id = :id
            ");
            $stmt->execute([
                ':status' => $newStatus,
                ':notes' => $notes,
                ':id' => $id
            ]);

            // If approved, restore stock for order items
            if ($action === 'approve') {
                $orderId = $return['order_id'];

                $stmt = $pdo->prepare("
                    SELECT oi.product_id, oi.quantity 
                    FROM order_items oi 
                    WHERE oi.order_id = :order_id
                ");
                $stmt->execute([':order_id' => $orderId]);
                $items = $stmt->fetchAll();

                foreach ($items as $item) {
                    $stmt = $pdo->prepare("
                        UPDATE products 
                        SET stock = stock + :quantity 
                        WHERE id = :product_id
                    ");
                    $stmt->execute([
                        ':quantity' => (int)$item['quantity'],
                        ':product_id' => (int)$item['product_id']
                    ]);
                }
            }

            $pdo->commit();

        } catch (Exception $e) {
            $pdo->rollBack();
            // Log error, but don't expose details
            error_log("Return update failed: " . $e->getMessage());
        }

        return new RedirectResponse('/returns');
    }
}
