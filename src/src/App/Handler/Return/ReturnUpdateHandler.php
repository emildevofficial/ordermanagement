<?php

declare(strict_types=1);

namespace App\Handler\Return;

use App\Database\Database;
use App\Helper\DateTimeHelper;
use App\Helper\Session;
use App\Helper\Permission;
use Laminas\Diactoros\Response\JsonResponse;
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
        $requestedStatus = (string)($data['status'] ?? '');
        $legacyAction = (string)($data['action'] ?? '');
        $adminNotes = trim($data['admin_notes'] ?? '');
        $redirectTo = (string)($data['redirect_to'] ?? '');
        $wantsJson = strtolower($request->getHeaderLine('X-Requested-With')) === 'xmlhttprequest'
            || strpos(strtolower($request->getHeaderLine('Accept')), 'application/json') !== false;

        // Admin only
        $deny = Permission::requireRole('admin');
        if ($deny instanceof RedirectResponse) {
            if ($wantsJson) {
                return new JsonResponse(['success' => false, 'error' => 'Unauthorized'], 403);
            }
            return $deny;
        }

        if ($requestedStatus === '' && in_array($legacyAction, ['approve', 'reject'], true)) {
            $requestedStatus = $legacyAction === 'approve' ? 'approved' : 'rejected';
        }

        if ($id <= 0 || !in_array($requestedStatus, ['approved', 'rejected'], true)) {
            if ($wantsJson) {
                return new JsonResponse(['success' => false, 'error' => 'Invalid request'], 400);
            }
            return new RedirectResponse('/returns');
        }

        $pdo = $this->db->getPdo();
        $this->ensureReturnTimestampColumns($pdo);

        try {
            $pdo->beginTransaction();

            // Verify return exists and current status
            $stmt = $pdo->prepare("SELECT order_id, status FROM returns WHERE id = :id FOR UPDATE");
            $stmt->execute([':id' => $id]);
            $return = $stmt->fetch();

            if (!$return) {
                $pdo->rollBack();
                if ($wantsJson) {
                    return new JsonResponse(['success' => false, 'error' => 'Return not found'], 404);
                }
                return new RedirectResponse('/returns');
            }

            $newStatus = $requestedStatus;
            $previousStatus = (string)$return['status'];

            if ($previousStatus === $newStatus) {
                $pdo->commit();
                if ($wantsJson) {
                    return new JsonResponse(['success' => true, 'status' => $newStatus]);
                }

                return $redirectTo === '/returns'
                    ? new RedirectResponse('/returns')
                    : new RedirectResponse('/returns/' . $id);
            }

            // Update return status and admin notes
            $stmt = $pdo->prepare(
                "UPDATE returns SET status = :status, admin_notes = :notes, updated_at = :updated_at WHERE id = :id"
            );
            $stmt->execute([
                ':status' => $newStatus,
                ':notes' => $adminNotes,
                ':updated_at' => DateTimeHelper::nowForStorage(),
                ':id' => $id,
            ]);

            // Keep stock aligned when the return decision changes.
            if ($newStatus === 'approved' || $previousStatus === 'approved') {
                $orderId = $return['order_id'];

                $stmt = $pdo->prepare(
                    "SELECT oi.product_id, oi.quantity FROM order_items oi WHERE oi.order_id = :order_id"
                );
                $stmt->execute([':order_id' => $orderId]);
                $items = $stmt->fetchAll();

                foreach ($items as $item) {
                    $stockDelta = $newStatus === 'approved' ? (int)$item['quantity'] : -(int)$item['quantity'];
                    $stmt = $pdo->prepare(
                        "UPDATE products SET stock = stock + :quantity WHERE id = :product_id"
                    );
                    $stmt->execute([
                        ':quantity' => $stockDelta,
                        ':product_id' => (int)$item['product_id'],
                    ]);
                }
            }

            $pdo->commit();
            if ($wantsJson) {
                return new JsonResponse(['success' => true, 'status' => $newStatus]);
            }
        } catch (\Exception $e) {
            $pdo->rollBack();
            error_log('Return update failed: ' . $e->getMessage());
            if ($wantsJson) {
                return new JsonResponse(['success' => false, 'error' => 'Failed to update return status'], 500);
            }
        }

        if ($redirectTo === '/returns') {
            return new RedirectResponse('/returns');
        }

        return new RedirectResponse('/returns/' . $id);
    }

    private function ensureReturnTimestampColumns(\PDO $pdo): void
    {
        $columns = [
            'admin_notes' => "ALTER TABLE returns ADD COLUMN admin_notes TEXT NULL",
            'updated_at' => "ALTER TABLE returns ADD COLUMN updated_at DATETIME NULL",
        ];

        foreach ($columns as $column => $sql) {
            $stmt = $pdo->prepare("SHOW COLUMNS FROM returns LIKE :column");
            $stmt->execute([':column' => $column]);
            if (!$stmt->fetch()) {
                $pdo->exec($sql);
            }
        }
    }
}
